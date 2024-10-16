import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, router, usePage} from '@inertiajs/react';
import {useEffect, useState} from "react";
import {Button, Card, CardBody, Chip, Typography} from "@material-tailwind/react";
import CreditCardIcon from "@/Icons/CreditCardIcon.jsx";
import {Helmet} from "react-helmet";
import PricingStructurePopup from "@/Components/PricingStructurePopup.jsx";
import ConfirmCardRemovalPopup from "@/Components/ManagePayments/ConfirmCardRemovalPopup.jsx";


export default function ManagePayments({auth, flash, errors}) {

    const [openPricingPopup, setOpenPricingPopup] = useState(false);

    const [openCardRemovalPopup, setOpenCardRemovalPopup] = useState(false);

    const {usage_details} = usePage().props;

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Manage Payments</h2>}
            flash={flash}
            errors={errors}
        >
            <Helmet>
                <meta httpEquiv="Content-Security-Policy" content="upgrade-insecure-requests;"/>
            </Helmet>

            <Head title="Manage Payments"/>

            <div className="py-20">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900  flex flex-col sm:flex-row">
                            <Card className="mt-6 w-full sm:w-1/3 border-black border-2 h-fit">
                                <CardBody className="justify-center flex">
                                    <Typography variant="h4" color="blue-gray" className="mb-2 text-center ">
                                        Available Free API Calls
                                    </Typography>
                                    <Chip value={auth.user.free_request_count} className="text-center w-1/4 text-3xl"
                                          variant="filled"
                                    ></Chip>
                                </CardBody>
                            </Card>
                            <Card className="mt-6 w-full sm:w-1/3 sm:ml-10 border-black border-2">
                                <CardBody>
                                    <Typography variant="h4" color="blue-gray" className="mb-2 text-center">
                                        Usage For The Current Billing Month
                                    </Typography>
                                    <Typography
                                        className={`text-sm text-center flex justify-center mt-1 ${auth.user.card_last_4 ? 'block' : 'hidden'}`}>
                                        (Next billing date : <span className="font-bold">{usage_details['billing_date']}</span>)
                                    </Typography>
                                    <Typography variant="h5" color="blue-gray"
                                                className="mb-2 text-center mt-10 font-extrabold flex justify-center">
                                        <span className="w-1/2">Total API calls</span>
                                        <Chip
                                            value={usage_details['usage'].toLocaleString('en-US', {
                                                maximumFractionDigits: 1
                                            })}
                                            className="text-center w-auto min-w-[25%] text-lg" variant="filled"
                                        ></Chip>
                                    </Typography>
                                    <Typography variant="h5" color="blue-gray"
                                                className="mb-2 text-center font-extrabold flex justify-center mt-5">
                                        <span className="w-1/2">Total Cost</span>
                                        <Chip
                                            value={'$' + usage_details['cost'].toLocaleString('en-US', {
                                                maximumFractionDigits: 5
                                            })}
                                            className="text-center w-auto text-lg" variant="filled"
                                        ></Chip>
                                    </Typography>
                                    <a href="#"
                                       className="text-sm py-1 pr-2 mb-2 text-center flex justify-center mt-5 transition-transform hover:scale-105 hover:underline"
                                       onClick={() => {
                                           setOpenPricingPopup(true)
                                       }}>
                                        See Pricing Structure
                                    </a>
                                    <PricingStructurePopup visible={openPricingPopup}
                                                           setVisible={setOpenPricingPopup}></PricingStructurePopup>
                                </CardBody>
                            </Card>
                            {auth.user.card_last_4 ?
                                (<Card className="mt-6 w-full sm:w-1/3  sm:ml-10 border-black border-2">
                                    <CardBody className="flex flex-col">
                                        <Typography variant="h4" color="blue-gray" className="mb-2 text-center">
                                            Payment Method
                                        </Typography>
                                        <Card className="bg-black">
                                            <Typography variant="h5" color="black"
                                                        className="mb-2 text-center text-white flex flex-col text-lg p-2">
                                                <CreditCardIcon></CreditCardIcon>
                                                xxxx xxxx xxxx {auth.user.card_last_4}
                                            </Typography>
                                        </Card>
                                        <Button size="sm"
                                                className="w-3/4 self-center mt-5 text-center justify-center"
                                                variant="outlined"
                                                onClick={() => {
                                                    router.visit(route('update-card'))
                                                }}>Change Payment Method</Button>
                                        <Button color="red" size="sm"
                                                className="w-3/4 self-center mt-5 text-center justify-center"
                                                variant="outlined" onClick={() => {
                                            setOpenCardRemovalPopup(true)
                                        }}>Remove Payment Method</Button>
                                        <ConfirmCardRemovalPopup visible={openCardRemovalPopup}
                                                                 setVisible={setOpenCardRemovalPopup}></ConfirmCardRemovalPopup>

                                    </CardBody>
                                </Card>) :
                                (<Card className="mt-6 w-full sm:w-1/3  sm:ml-10 border-black border-2">
                                    <CardBody className="flex flex-col">
                                        <Typography variant="h4" color="blue-gray" className="mb-2 text-center">
                                            Payment Method
                                        </Typography>
                                        <Typography variant="h6" color="red" className="mb-2 text-center mt-6">
                                            No Payment Method Added
                                        </Typography>
                                        <Button size="sm"
                                                className="w-3/4 self-center mt-5 text-center justify-center"
                                                variant="outlined"
                                                onClick={() => {
                                                    router.visit(route('save-card'))
                                                }}
                                        >Add payment method</Button>
                                    </CardBody>
                                </Card>)
                            }
                        </div>
                        {/*<div className="p-6">*/}
                        {/*    <InvoicesTable></InvoicesTable>*/}
                        {/*</div>*/}
                    </div>
                </div>
            </div>

        </AuthenticatedLayout>
    );
}
