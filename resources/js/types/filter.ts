export interface BaseFilter {
	q: string | null;
	sort: string | null;
	direction: 'asc' | 'desc' | null;
	page: number | null;
	per_page: number | null;
	[key: string]: unknown;
}

export interface SortOption {
	label: string;
	value: string;
}
export interface SortOrderFilterProps<T extends BaseFilter> {
	filters: T;
	apply: (filters: Partial<T>) => void;
	sortOptions: SortOption[];
}
