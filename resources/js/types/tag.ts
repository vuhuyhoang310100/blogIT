import { Pagination } from './pagination';

export interface SingleTag {
	id: number;
	name: string;
	slug?: string;
	created_at: string;
	updated_at: string;
}
export interface Tag extends Pagination {
	data: SingleTag[];
}
