import { SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

interface SeoProps {
	title?: string;
	description?: string;
	keywords?: string;
	image?: string;
	type?: string;
}

export function SeoHead({
	title,
	description,
	keywords,
	image,
	type,
}: Partial<SeoProps>) {
	const { seo, pageSeo } = usePage<SharedData>().props;

	const pSeo = pageSeo || {};
	const sMeta = seo.meta;
	const sOg = seo.open_graph;

	const siteName = sOg.site_name;

	// Priority: Component > Page override > Shared default
	const finalTitle = title || pSeo.title || sMeta.title;

	const displayTitle = finalTitle.includes(siteName)
		? finalTitle
		: `${finalTitle} - ${siteName}`;

	const finalDescription =
		description || pSeo.description || sMeta.description;

	const finalKeywords = keywords || pSeo.keywords || sMeta.keywords;

	const finalImage = image || pSeo.image || sMeta.image;

	const finalType = type || pSeo.type || sOg.type;

	const pagination = pSeo.pagination;

	const url = typeof window !== 'undefined' ? window.location.href : '';

	const canonical = url.replace('?page=1', '');

	return (
		<Head>
			<title>{displayTitle}</title>

			<meta name="description" content={finalDescription} />
			<meta name="keywords" content={finalKeywords} />

			{/* Canonical */}
			<link rel="canonical" href={canonical} />

			{/* Pagination SEO */}
			{pagination && pagination.prev && (
				<link rel="prev" href={pagination.prev} />
			)}

			{pagination && pagination.next && (
				<link rel="next" href={pagination.next} />
			)}

			{/* OpenGraph */}
			<meta property="og:title" content={displayTitle} />
			<meta property="og:description" content={finalDescription} />
			<meta property="og:type" content={finalType} />
			<meta property="og:site_name" content={siteName} />
			<meta property="og:image" content={finalImage} />

			{/* Twitter */}
			<meta name="twitter:card" content="summary_large_image" />
			<meta name="twitter:title" content={displayTitle} />
			<meta name="twitter:description" content={finalDescription} />
			<meta name="twitter:image" content={finalImage} />
		</Head>
	);
}
