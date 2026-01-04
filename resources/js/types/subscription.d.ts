export interface SubscriptionPlan {
    uuid: string;
    name: string;
    slug: string;
    price: number;
    price_formatted: string;
    price_cents: number;
    billing_period: string;
    features: Record<string, number | boolean>;
    description: string | null;
    max_users: number;
    is_active: boolean;
    is_free: boolean;
    is_premium: boolean;
    is_family: boolean;
    plan_type: string;
    created_at: string;
    updated_at: string;
}

export interface Subscription {
    uuid: string;
    user_id: number;
    plan: SubscriptionPlan;
    started_at: string;
    ends_at: string | null;
    cancelled_at: string | null;
    status: string;
    status_label: string;
    status_color: 'success' | 'destructive' | 'secondary' | 'warning';
    payment_gateway: string | null;
    external_subscription_id: string | null;
    is_active: boolean;
    is_cancelled: boolean;
    is_expired: boolean;
    is_pending: boolean;
    on_grace_period: boolean;
    days_remaining: number;
    can_resume: boolean;
    can_cancel: boolean;
    created_at: string;
    updated_at: string;
}
