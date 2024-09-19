import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';
import RequestsOverTimeGraph from "@/Components/Dashboard/RequestsOverTimeGraph.jsx";
import ProfanityFrequencyGraph from "@/Components/Dashboard/ProfanityFrequencyGraph.jsx";
import PercentageBannedWordsCategory from "@/Components/Dashboard/PercentageBannedWordsCategory.jsx";
import FilterHistory from "@/Components/Dashboard/RequestDetailsTable.jsx";
import RequestDetailsTable from "@/Components/Dashboard/RequestDetailsTable.jsx";


export default function Dashboard({auth, flash, errors}) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
            flash={flash}
            errors={errors}
        >
            <Head title="Dashboard"/>

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="w-full justify-between mt-6 hidden sm:flex">
                                <div className="w-1/2">
                                    <ProfanityFrequencyGraph></ProfanityFrequencyGraph>
                                </div>
                                <div className="w-2/5">
                                    <PercentageBannedWordsCategory></PercentageBannedWordsCategory>
                                </div>
                            </div>
                            <div className="mt-6">
                                <RequestDetailsTable></RequestDetailsTable>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
