import { useEffect } from 'react';
import Checkbox from '@/Components/BreezeComponents/Checkbox.jsx';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/BreezeComponents/InputError.jsx';
import InputLabel from '@/Components/BreezeComponents/InputLabel.jsx';
import PrimaryButton from '@/Components/BreezeComponents/PrimaryButton.jsx';
import TextInput from '@/Components/BreezeComponents/TextInput.jsx';
import { Head, Link, useForm } from '@inertiajs/react';
import {Button} from "@material-tailwind/react";

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();

        post(route('login'));
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

            {status && <div className="mb-4 font-medium text-sm text-green-600">{status}</div>}

            <form onSubmit={submit}>

                <a className="flex justify-center" href={route('google.redirect')}>
                    <Button
                        size="md"
                        variant="outlined"
                        color="blue-gray"
                        className="flex text-center justify-center w-full">
                        <img src="https://docs.material-tailwind.com/icons/google.svg" alt="metamask"
                             className="h-4 w-4 mr-2"/>
                        Login with Google
                    </Button>
                </a>

                <br/>

                <div className="flex justify-center">
                    <span className="text-center">or</span>
                </div>

                <div>
                    <InputLabel htmlFor="email" value="Email"/>

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-xz1 block w-full"
                        autoComplete="username"
                        isFocused={true}
                        onChange={(e) => setData('email', e.target.value)}
                    />

                    <InputError message={errors.email} className="mt-2"/>
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="password" value="Password"/>

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="current-password"
                        onChange={(e) => setData('password', e.target.value)}
                    />

                    <InputError message={errors.password} className="mt-2"/>
                </div>

                <div className="block mt-4">
                    <label className="flex items-center">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(e) => setData('remember', e.target.checked)}
                        />
                        <span className="ms-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <div className="flex items-center justify-end mt-4">
                    {canResetPassword && (
                        <Link
                            href={route('password.request')}
                            className="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Forgot your password?
                        </Link>
                    )}

                    <PrimaryButton className="ms-4" disabled={processing}>
                        Log in
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
