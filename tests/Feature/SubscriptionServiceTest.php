<?php

use App\Domain\Subscriptions\Services\SubscriptionService;
use App\Enums\PlanTypeEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->subscriptionService = app(SubscriptionService::class);

    // Create subscription plans
    $this->freePlan = SubscriptionPlan::factory()->create([
        'slug' => PlanTypeEnum::FREE->value,
        'price_cents' => 0,
    ]);

    $this->premiumPlan = SubscriptionPlan::factory()->create([
        'slug' => PlanTypeEnum::PREMIUM->value,
        'price_cents' => 1990, // R$ 19,90
    ]);

    $this->familyPlan = SubscriptionPlan::factory()->create([
        'slug' => PlanTypeEnum::FAMILY->value,
        'price_cents' => 2990, // R$ 29,90
    ]);
});

test('can create free subscription', function () {
    $subscription = $this->subscriptionService->create(
        $this->user,
        PlanTypeEnum::FREE->value
    );

    expect($subscription)->toBeInstanceOf(Subscription::class)
        ->and($subscription->plan->slug)->toBe(PlanTypeEnum::FREE->value)
        ->and($subscription->status)->toBe(SubscriptionStatusEnum::ACTIVE->value)
        ->and($subscription->user_id)->toBe($this->user->id);

    // Free plan should be activated immediately
    $this->user->refresh();
    expect($this->user->current_subscription_id)->toBe($subscription->id);
});

test('can create premium subscription', function () {
    $subscription = $this->subscriptionService->create(
        $this->user,
        PlanTypeEnum::PREMIUM->value
    );

    expect($subscription)->toBeInstanceOf(Subscription::class)
        ->and($subscription->plan->slug)->toBe(PlanTypeEnum::PREMIUM->value)
        ->and($subscription->status)->toBe(SubscriptionStatusEnum::PENDING->value)
        ->and($subscription->user_id)->toBe($this->user->id);

    // Premium plan should be pending until payment
    $this->user->refresh();
    expect($this->user->current_subscription_id)->toBeNull();
});

test('can activate subscription', function () {
    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'subscription_plan_id' => $this->premiumPlan->id,
        'status' => SubscriptionStatusEnum::PENDING->value,
    ]);

    $this->subscriptionService->activate($subscription);

    $subscription->refresh();
    expect($subscription->status)->toBe(SubscriptionStatusEnum::ACTIVE->value)
        ->and($subscription->started_at)->not->toBeNull();

    $this->user->refresh();
    expect($this->user->current_subscription_id)->toBe($subscription->id);
});

test('can cancel subscription', function () {
    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'subscription_plan_id' => $this->premiumPlan->id,
        'status' => SubscriptionStatusEnum::ACTIVE->value,
        'started_at' => now(),
    ]);

    $this->user->update(['current_subscription_id' => $subscription->id]);

    $this->subscriptionService->cancel($subscription);

    $subscription->refresh();
    expect($subscription->status)->toBe(SubscriptionStatusEnum::CANCELLED->value)
        ->and($subscription->cancelled_at)->not->toBeNull();
});

test('can upgrade subscription', function () {
    // Create active free subscription
    $freeSubscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'subscription_plan_id' => $this->freePlan->id,
        'status' => SubscriptionStatusEnum::ACTIVE->value,
        'started_at' => now(),
    ]);

    $this->user->update(['current_subscription_id' => $freeSubscription->id]);

    // Upgrade to premium
    $newSubscription = $this->subscriptionService->upgrade(
        $this->user,
        PlanTypeEnum::PREMIUM->value
    );

    expect($newSubscription)->toBeInstanceOf(Subscription::class)
        ->and($newSubscription->plan->slug)->toBe(PlanTypeEnum::PREMIUM->value)
        ->and($newSubscription->status)->toBe(SubscriptionStatusEnum::PENDING->value);

    // Old subscription should be cancelled
    $freeSubscription->refresh();
    expect($freeSubscription->status)->toBe(SubscriptionStatusEnum::CANCELLED->value);
});

test('can downgrade subscription', function () {
    // Create active premium subscription
    $premiumSubscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'subscription_plan_id' => $this->premiumPlan->id,
        'status' => SubscriptionStatusEnum::ACTIVE->value,
        'started_at' => now(),
        'ends_at' => now()->addMonth(),
    ]);

    $this->user->update(['current_subscription_id' => $premiumSubscription->id]);

    // Downgrade to free (scheduled at end of period)
    $newSubscription = $this->subscriptionService->downgrade(
        $this->user,
        PlanTypeEnum::FREE->value
    );

    expect($newSubscription)->toBeInstanceOf(Subscription::class)
        ->and($newSubscription->plan->slug)->toBe(PlanTypeEnum::FREE->value)
        ->and($newSubscription->status)->toBe(SubscriptionStatusEnum::PENDING->value);

    // Old subscription should have an end date set
    $premiumSubscription->refresh();
    expect($premiumSubscription->ends_at)->not->toBeNull();
});

test('can resume cancelled subscription', function () {
    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'subscription_plan_id' => $this->premiumPlan->id,
        'status' => SubscriptionStatusEnum::CANCELLED->value,
        'cancelled_at' => now(),
        'ends_at' => now()->addMonth(),
    ]);

    $this->subscriptionService->resume($subscription);

    $subscription->refresh();
    expect($subscription->status)->toBe(SubscriptionStatusEnum::ACTIVE->value)
        ->and($subscription->cancelled_at)->toBeNull();
});

test('subscription is on grace period when cancelled but not expired', function () {
    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'subscription_plan_id' => $this->premiumPlan->id,
        'status' => SubscriptionStatusEnum::CANCELLED->value,
        'cancelled_at' => now(),
        'ends_at' => now()->addDays(10), // Still valid for 10 days
    ]);

    expect($subscription->onGracePeriod())->toBeTrue()
        ->and($subscription->isActive())->toBeFalse();
});

test('user has active subscription returns correct plan limits', function () {
    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'subscription_plan_id' => $this->premiumPlan->id,
        'status' => SubscriptionStatusEnum::ACTIVE->value,
        'started_at' => now(),
    ]);

    $this->user->update(['current_subscription_id' => $subscription->id]);

    $limits = $this->user->getPlanLimits();

    expect($limits)->toBeArray()
        ->and($limits['max_wallets'])->toBe(10)
        ->and($limits['max_categories'])->toBe(50)
        ->and($limits['financial_reports'])->toBeTrue();
});

test('user without subscription gets free plan limits', function () {
    $limits = $this->user->getPlanLimits();

    expect($limits)->toBeArray()
        ->and($limits['max_wallets'])->toBe(2)
        ->and($limits['max_categories'])->toBe(10)
        ->and($limits['financial_reports'])->toBeFalse();
});
