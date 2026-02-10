import { Input } from '@/components/ui/input';
import { Search } from 'lucide-react';

export function SearchBox({
	value,
	defaultValue,
	placeholder = 'Search...',
	onSearch,
	onChange,
}: {
	value?: string;
	defaultValue?: string;
	placeholder?: string;
	onSearch: (value: string) => void;
	onChange?: (value: string) => void;
}) {
	return (
		<div className="relative">
			<Search className="absolute top-1/2 left-2.5 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
			<Input
				type="search"
				placeholder={placeholder}
				value={value}
				defaultValue={defaultValue}
				onChange={(e) => {
					onChange?.(e.target.value);
				}}
				onKeyDown={(e) => {
					if (e.key === 'Enter') {
						onSearch((e.target as HTMLInputElement).value);
					}
				}}
				className="w-full pl-9"
			/>
		</div>
	);
}
