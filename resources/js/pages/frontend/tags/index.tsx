import { PageHeader } from '@/components/frontend/page-header';
import { TagGridSection } from '@/components/frontend/tag-grid-section';
import { SearchBox } from '@/components/search-box';
import { useDebounce } from '@/hooks/use-debounce';
import GuestLayout from '@/layouts/frontend/guest-layout';
import articlesRoute from '@/routes/articles';
import { ResourceCollection, SingleTag } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Hash, Sparkles, TrendingUp, Zap } from 'lucide-react';
import { useMemo, useState } from 'react';

interface TagsIndexProps {
	tags?: ResourceCollection<SingleTag>;
}

export default function TagsIndex({ tags }: TagsIndexProps) {
	const [searchQuery, setSearchQuery] = useState('');
	const debouncedSearchQuery = useDebounce(searchQuery, 300);

	const filteredTags = useMemo(() => {
		return (tags?.data || []).filter((tag) =>
			tag.name.toLowerCase().includes(debouncedSearchQuery.toLowerCase()),
		);
	}, [tags?.data, debouncedSearchQuery]);

	function onSearch(value: string) {
		setSearchQuery(value);
	}

	const allTags = tags?.data || [];

	return (
		<GuestLayout>
			<Head title="Browse Topics - BlogIT" />

			<PageHeader
				heading="Browse by"
				highlight="Topic"
				description="Explore our curated collection of articles categorized by the technologies and paradigms that shape the modern web."
				badge="Tags Collection"
			/>

			<div className="container mx-auto px-6 py-12">
				<div className="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
					<TagGridSection
						title="Trending Topics"
						icon={TrendingUp}
						tags={allTags}
					/>
					<TagGridSection
						title="Popular Tags"
						icon={Zap}
						tags={allTags}
					/>
					<TagGridSection
						title="Recently Added"
						icon={Sparkles}
						tags={allTags}
					/>
				</div>
			</div>

			<div className="container mx-auto px-6 py-24">
				<div className="mb-10 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
					<div className="flex items-center gap-4">
						<div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary shadow-lg shadow-primary/20">
							<Hash className="h-6 w-6 text-primary-foreground" />
						</div>
						<div>
							<h2 className="text-2xl font-black tracking-tight tracking-widest uppercase">
								All Tags
							</h2>
							<p className="text-sm font-medium text-muted-foreground">
								Discover everything we cover
							</p>
						</div>
					</div>
					<div className="relative w-full md:w-80">
						<SearchBox
							value={searchQuery}
							placeholder="Search tags..."
							onSearch={onSearch}
							onChange={onSearch}
						/>
					</div>
				</div>

				<div className="grid grid-cols-2 gap-4 rounded-[2rem] border border-border/40 bg-muted/20 p-8 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6">
					{filteredTags.length > 0 ? (
						filteredTags.map((tag) => (
							<Link
								key={tag.id}
								href={articlesRoute.index.url({
									query: { tag: tag.slug },
								})}
								className="group flex items-center gap-3 rounded-2xl border border-border/50 bg-card px-4 py-3 transition-all hover:-translate-y-1 hover:border-primary/40 hover:shadow-md"
							>
								<Hash className="h-4 w-4 text-muted-foreground/60 transition-colors group-hover:text-primary" />
								<span className="text-sm font-bold tracking-wide text-muted-foreground/80 transition-colors group-hover:text-foreground">
									{tag.name}
								</span>
							</Link>
						))
					) : (
						<div className="col-span-full py-16 text-center">
							<div className="mb-4 flex justify-center">
								<div className="rounded-full bg-muted p-4">
									<Hash className="h-8 w-8 text-muted-foreground/40" />
								</div>
							</div>
							<p className="text-lg font-medium text-muted-foreground">
								No tags found matching "{searchQuery}"
							</p>
							<button
								onClick={() => setSearchQuery('')}
								className="mt-2 cursor-pointer text-sm font-bold text-primary hover:underline"
							>
								Clear search
							</button>
						</div>
					)}
				</div>
			</div>
		</GuestLayout>
	);
}
