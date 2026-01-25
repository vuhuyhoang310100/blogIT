import { SelectFilter } from '@/components/select-filter';
import {
	POST_STATUS_DRAFT,
	POST_STATUS_PENDING,
	POST_STATUS_PUBLISHED,
	POST_STATUSES,
	TRASHED_OPTIONS,
} from '@/constants';
import { enumOrNull, isEnumValue } from '@/lib/enum';
import { PostFilters } from '@/types';
import { Activity, Eye } from 'lucide-react';

const ALL = '__all__';

export function StatusFilter({
	filters,
	apply,
}: {
	filters: PostFilters;
	apply: (v: Partial<PostFilters>) => void;
}) {
	return (
		<SelectFilter
			title="Status"
			icon={<Activity className="size-4 text-primary" />}
			value={filters.status?.toString() ?? ALL}
			onValueChange={(v) => {
				if (v === ALL) {
					apply({ status: null });
					return;
				}

				const numValue = Number(v);
				if (isEnumValue(numValue, POST_STATUSES)) {
					apply({ status: numValue });
				}
			}}
			options={[
				{ label: 'All Status', value: ALL },
				{ label: 'Draft', value: POST_STATUS_DRAFT.toString() },
				{ label: 'Pending', value: POST_STATUS_PENDING.toString() },
				{ label: 'Published', value: POST_STATUS_PUBLISHED.toString() },
			]}
			placeholder="All status"
		/>
	);
}

export function VisibilityFilter({
	filters,
	apply,
}: {
	filters: PostFilters;
	apply: (v: Partial<PostFilters>) => void;
}) {
	return (
		<SelectFilter
			title="Visibility"
			icon={<Eye className="size-4 text-primary" />}
			value={filters.trashed ?? ALL}
			onValueChange={(v) =>
				apply({
					trashed: v === ALL ? null : enumOrNull(v, TRASHED_OPTIONS),
				})
			}
			options={[
				{ label: 'Active', value: ALL },
				{ label: 'With Trashed', value: 'with' },
				{ label: 'Trashed Only', value: 'only' },
			]}
			placeholder="Active only"
		/>
	);
}
