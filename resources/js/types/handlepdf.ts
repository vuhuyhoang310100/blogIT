import { Pagination } from './pagination';

export interface SingleHandlepdf {
    id: number;
    title: string;
    file_path: string;
    user_id: number;
    created_at: string;
    updated_at: string;
}

export interface Handlepdf extends Pagination {
    data: SingleHandlepdf[];
}

export interface HandlepdfFormData {
    title: string;
    file_path: string;
    user_id: number;
}
