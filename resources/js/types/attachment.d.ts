export interface Attachment {
    id: number;
    uuid: string;
    user_id: number;
    attachable_id: number;
    attachable_type: string;
    file_name: string;
    file_path: string;
    original_name: string;
    file_size: number;
    mime_type: string;
    extension: string;
    created_at: string;
    updated_at: string;
    // Accessors
    file_size_formatted?: string;
    download_url?: string;
    is_image?: boolean;
    is_pdf?: boolean;
}

export interface AttachmentStats {
    count: number;
    limit: number;
    limit_label: string;
    total_storage: number;
    total_storage_formatted: string;
    can_upload: boolean;
}
