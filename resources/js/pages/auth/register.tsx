import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { InputPassword } from '@/components/ui/input-password';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

export default function Register() {
	return (
		<AuthLayout
			title="Create an account"
			description="Enter your details below to create your account"
		>
			<Head title="Register" />
			<Form
				{...RegisteredUserController.store.form()}
				resetOnSuccess={['password', 'password_confirmation']}
				disableWhileProcessing
				className="flex flex-col gap-6"
			>
				{({ processing, errors }) => (
					<>
						<div className="grid gap-6">
							<div className="grid gap-2">
								<Label htmlFor="name">Name</Label>
								<Input
									id="name"
									type="text"
									required
									autoFocus
									tabIndex={1}
									autoComplete="name"
									name="name"
									placeholder="Full name"
								/>
								<InputError
									message={errors.name}
									className="mt-2"
								/>
							</div>

							<div className="grid gap-2">
								<Label htmlFor="email">Email address</Label>
								<Input
									id="email"
									type="email"
									required
									tabIndex={2}
									autoComplete="email"
									name="email"
									placeholder="email@example.com"
								/>
								<InputError message={errors.email} />
							</div>

							<div className="grid gap-2">
								<Label htmlFor="password">Password</Label>
								<InputPassword
									id="password"
									name="password"
									required
									tabIndex={3}
									autoComplete="new-password"
									placeholder="Password"
									autoHideWhen={processing}
								/>
								<InputError message={errors.password} />
							</div>

							<div className="grid gap-2">
								<Label htmlFor="password_confirmation">
									Confirm password
								</Label>
								<InputPassword
									id="password_confirmation"
									required
									name="password_confirmation"
									tabIndex={4}
									autoComplete="current-password"
									placeholder="Confirm password"
									autoHideWhen={processing}
								/>
								<InputError
									message={errors.password_confirmation}
								/>
							</div>

							<Button
								type="submit"
								className="mt-2 w-full hover:cursor-pointer"
								tabIndex={5}
								data-test="register-user-button"
							>
								{processing && (
									<LoaderCircle className="h-4 w-4 animate-spin" />
								)}
								Create account
							</Button>
						</div>

						<div className="text-center text-sm text-muted-foreground">
							Already have an account?{' '}
							<TextLink href={login()} tabIndex={6}>
								Log in
							</TextLink>
						</div>
					</>
				)}
			</Form>
		</AuthLayout>
	);
}
