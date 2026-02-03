import { Button } from '@/components/ui/button';

import {
	Card,
	CardAction,
	CardContent,
	CardHeader,
	CardTitle,
} from '@/components/ui/card';

import AppLayout from '@/layouts/app-layout';
import { flattenCategories } from '@/lib/category-utils';

import TablePagination from '@/components/table-pagination';
import { BreadcrumbItem } from '@/types';
import { Category, SingleCategory } from '@/types/category';
import { Head, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import CreateCategoryDialog from '../categories/partials/CreateCategoryDialog';
import categories from '../categories';
import CategoriesTable from '../categories/partials/CategoriesTable';
import HandlePdfsTable from './partials/HandlePdfsTable';
import { SingleHandlepdf } from '@/types/handlepdf';

const breadcrumbs: BreadcrumbItem[] = [
	{
		title: 'HandlePdfs',
		href: '/handlepdfs',
	},
];

export default function HandlePdfs({}) {

	const [showCreateDialog, setShowCreateDialog] = useState(false);
	const [showEditDialog, setShowEditDialog] = useState(false);
	const [editingHandlePdf, setEditingHandlePdf] =
		useState<SingleHandlepdf | null>(null);

	const flatCategories = useMemo(() => {
		if (!Array.isArray(categories)) return [];
		return flattenCategories(categories);
	}, [categories]);

	const handleEdit = (handlePdf: SingleHandlepdf) => {
		setEditingHandlePdf(handlePdf);
		setShowEditDialog(true);
	};

	const handleCloseEditDialog = (open: boolean) => {
		if (!open) {
			setEditingHandlePdf(null);
		}
		setShowEditDialog(open);
	};

	return (
		<AppLayout breadcrumbs={breadcrumbs}>
			<div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
				<Head title="HandlePdfs" />

				<Card>
					<CardHeader className="flex flex-row items-center justify-between">
						<CardTitle>Category Management</CardTitle>
						<CardAction>
							<Button onClick={() => setShowCreateDialog(true)}>
								Add new
							</Button>
						</CardAction>
					</CardHeader>
					<hr />
					<CardContent>
						<HandlePdfsTable
							handlePdfs={handlePdfs}
							onEdit={handleEdit}
						/>
						<TablePagination
							total={paginatedCategories.total}
							from={paginatedCategories.from}
							to={paginatedCategories.to}
							links={paginatedCategories.links}
						/>
					</CardContent>
				</Card>

				<CreateCategoryDialog
					open={showCreateDialog}
					onOpenChange={setShowCreateDialog}
					flatCategories={flatCategories}
				/>
			</div>
		</AppLayout>
	);
}
