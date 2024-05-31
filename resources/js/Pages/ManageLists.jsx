import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';
import ManageListTable from "@/Components/ManageListTable.jsx";
import {useState} from "react";


export default function ManageLists({auth}) {

    const [open, setOpen] = useState(false);

    const handleOpen = () => setOpen(!open);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Manage Blacklist and Whitelist</h2>}
        >
            <Head title="Dashboard"/>

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <ManageListTable type={"blacklist"}></ManageListTable>
                            <ManageListTable type={"whitelist"}></ManageListTable>
                        </div>
                    </div>
                </div>
            </div>

        </AuthenticatedLayout>
    );
}
