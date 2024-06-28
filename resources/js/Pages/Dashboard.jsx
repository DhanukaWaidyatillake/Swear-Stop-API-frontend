import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';
import RequestsOverTimeGraph from "@/Components/RequestsOverTimeGraph.jsx";
import ProfanityFrequencyGraph from "@/Components/ProfanityFrequencyGraph.jsx";
import NoOfBannedWordsByMetaDataGraph from "@/Components/NoOfBannedWordsByMetaDataGraph.jsx";
import RequestsTable from "@/Components/RequestsTable.jsx";


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
                            <RequestsOverTimeGraph></RequestsOverTimeGraph>
                            <div className="flex w-full justify-between mt-6">
                                <div className="w-1/2">
                                    <ProfanityFrequencyGraph></ProfanityFrequencyGraph>
                                </div>
                                <div className="w-1/3">
                                    <NoOfBannedWordsByMetaDataGraph></NoOfBannedWordsByMetaDataGraph>
                                </div>
                            </div>
                            <div className="mt-6">
                                <RequestsTable></RequestsTable>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
