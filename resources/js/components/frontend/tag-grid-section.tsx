import { cn } from '@/lib/utils';
import articlesRoute from '@/routes/articles';
import { SingleTag } from '@/types';
import { Link } from '@inertiajs/react';
import { Hash, LucideIcon } from 'lucide-react';

const colors = [
	'from-blue-500/20 to-cyan-500/20',
	'from-red-500/20 to-orange-500/20',
	'from-purple-500/20 to-indigo-500/20',
	'from-green-500/20 to-emerald-500/20',
	'from-blue-600/20 to-indigo-600/20',
	'from-pink-500/20 to-rose-500/20',
	'from-slate-500/20 to-slate-700/20',
	'from-yellow-500/20 to-amber-600/20',
];

interface TagCardProps {
	tag: SingleTag;
	index: number;
}

export function TagCard({ tag, index }: TagCardProps) {
	const colorClass = colors[index % colors.length];

	return (
		<Link
			href={articlesRoute.index.url({
				query: { tag: tag.slug },
			})}
			className="group relative flex flex-col items-start overflow-hidden rounded-2xl border border-border/50 bg-card p-5 transition-all duration-500 hover:-translate-y-1.5 hover:border-primary/30 hover:shadow-xl sm:p-4"
		>
			<div
				className={cn(
					'absolute -top-8 -right-8 h-24 w-24 rounded-full bg-gradient-to-br blur-2xl transition-all duration-700 group-hover:scale-150 group-hover:opacity-40',
					colorClass,
				)}
			/>

			<div className="relative z-10 mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-primary/5 transition-colors group-hover:bg-primary/10">
				<Hash className="h-5 w-5 text-primary transition-transform duration-500 group-hover:rotate-12" />
			</div>

			<div className="relative z-10">
				<h3 className="mb-1 text-lg font-bold tracking-tight text-foreground/90 transition-colors group-hover:text-primary">
					{tag.name}
				</h3>
				<p className="text-[10px] font-black tracking-[0.15em] text-muted-foreground/50 uppercase transition-colors group-hover:text-muted-foreground/70">
					{tag.posts_count || 0} Articles
				</p>
			</div>
		</Link>
	);
}

interface TagGridSectionProps {
	title: string;
	icon: LucideIcon;
	tags: SingleTag[];
	className?: string;
}

export function TagGridSection({
	title,
	icon: Icon,
	tags,
	className,
}: TagGridSectionProps) {
	return (
		<div
			className={cn(
				'rounded-3xl border border-border/40 bg-muted/30 p-6 backdrop-blur-sm lg:p-8',
				className,
			)}
		>
			<div className="mb-8 flex items-center justify-between">
				<div className="flex items-center gap-3">
					<div className="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
						<Icon className="h-5 w-5" />
					</div>
					<h2 className="text-lg font-black tracking-widest text-foreground/80 uppercase">
						{title}
					</h2>
				</div>
			</div>

			<div className="grid grid-cols-2 gap-4">
				{tags.slice(0, 6).map((tag, index) => (
					<TagCard key={tag.id || tag.name} tag={tag} index={index} />
				))}
				{tags.length === 0 && (
					<div className="col-span-2 flex h-32 items-center justify-center rounded-2xl border border-dashed border-border/60 text-sm text-muted-foreground">
						No topics found
					</div>
				)}
			</div>
		</div>
	);
}
