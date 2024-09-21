import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import {Head} from '@inertiajs/react';
import APIKeyForm from "@/Pages/Profile/Partials/APIKeyForm.jsx";

export default function Edit({auth, api_key, flash, errors}) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>}
            flash={flash}
            errors={errors}
        >

            <Head title="Profile"/>

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <APIKeyForm
                            className="max-w-xl"
                            api_key={api_key}
                        />
                    </div>

                    <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <UpdatePasswordForm className="max-w-xl"/>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
