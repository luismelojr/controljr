import type { Subscription } from './subscription';

export interface Payment {
    id: number;
    uuid: string;
    user_id: number;
    subscription_id: number | null;
    amount_cents: number;
    amount: number;
    amount_formatted: string;
    status: 'pending' | 'confirmed' | 'received' | 'overdue' | 'refunded' | 'cancelled';
    payment_method: 'pix' | 'boleto' | 'credit_card';
    payment_gateway: string;
    external_payment_id: string | null;
    invoice_url: string | null;
    pix_qr_code: string | null;
    pix_copy_paste: string | null;
    boleto_barcode: string | null;
    due_date: string | null;
    confirmed_at: string | null;
    paid_at: string | null;
    created_at: string;
    updated_at: string;
    subscription?: Subscription;
}

export interface PaymentPageProps {
    payment: Payment;
}

export interface PaymentMethodPageProps {
    subscription: Subscription;
    paymentMethods: {
        pix: boolean;
        boleto: boolean;
        credit_card: boolean;
    };
}

export interface PaymentIndexPageProps {
    payments: {
        data: Payment[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}
