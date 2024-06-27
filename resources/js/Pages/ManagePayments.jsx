import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, router} from '@inertiajs/react';
import {useEffect, useState} from "react";
import {Button, Card, CardBody, Chip, Typography} from "@material-tailwind/react";
import CreditCardIcon from "@/Icons/CreditCardIcon.jsx";
import InvoicesTable from "@/Components/InvoicesTable.jsx";
import {Helmet} from "react-helmet";


export default function ManagePayments({auth}) {

    const [open, setOpen] = useState(false);

    const handleOpen = () => setOpen(!open);

    useEffect(() => {
        // Load the Paddle script dynamically
        const script = document.createElement('script');
        script.src = 'https://cdn.paddle.com/paddle/v2/paddle.js';
        script.async = true;
        document.body.appendChild(script);

        script.onload = () => {
            if (window.Paddle) {

                window.Paddle.Environment.set("sandbox");

                window.Paddle.Initialize({
                    token: 'test_417115b54e528a1ec94a04c6c4b',  // replace with your actual client-side token
                    eventCallback: function (data) {
                        switch (data.name) {
                            case "checkout.completed":
                                router.post('/card-saved-successfully', data, {
                                    'preserveScroll': true
                                })
                                break;
                            case "checkout.error":
                                router.post('/card-saved-failed', data, {
                                    'preserveScroll': true
                                })
                                break;
                            case "checkout.closed":
                                window.location.reload()
                            default:
                                break;
                        }
                    }
                });
            }
        };

        return () => {
            // Cleanup the script when the component unmounts
            document.body.removeChild(script);
        };
    }, []);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Manage Payments</h2>}
        >
            <Helmet>
                <meta httpEquiv="Content-Security-Policy" content="upgrade-insecure-requests;"/>
            </Helmet>

            <Head title="Dashboard"/>

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900  flex flex-col sm:flex-row">
                            <Card className="mt-6 w-full sm:w-1/3 border-black border-2 h-fit">
                                <CardBody className="justify-center flex">
                                    <Typography variant="h4" color="blue-gray" className="mb-2 text-center ">
                                        Remaining Free Requests
                                    </Typography>
                                    <Chip value={"97"} className="text-center w-1/4 text-3xl" variant="filled"
                                    ></Chip>
                                </CardBody>
                            </Card>
                            <Card className="mt-6 w-full sm:w-1/3 sm:ml-10 border-black border-2">
                                <CardBody>
                                    <Typography variant="h4" color="blue-gray" className="mb-2 text-center">
                                        Current Usage For This Month
                                    </Typography>
                                    <Typography variant="h5" color="blue-gray"
                                                className="mb-2 text-center mt-10 font-extrabold flex justify-center">
                                        <span className="w-1/2">Total API calls</span>
                                        <Chip value={"67"} className="text-center w-1/4 text-lg" variant="filled"
                                        ></Chip>
                                    </Typography>
                                    <Typography variant="h5" color="blue-gray"
                                                className="mb-2 text-center font-extrabold flex justify-center mt-5">
                                        <span className="w-1/2">Total Cost</span>
                                        <Chip value={"$5.67"} className="text-center w-1/4 text-lg" variant="filled"
                                        ></Chip>
                                    </Typography>
                                </CardBody>
                            </Card>
                            {!auth.user.is_subscribed ?
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
                                                variant="outlined" onClick={() => {
                                                        axios.get('/pre-payment-method-change').
                                                    then(response => {
                                                            window.Paddle.Checkout.open({
                                                                settings: {
                                                                    displayMode: "overlay",
                                                                    theme: "light",
                                                                    locale: "en",
                                                                    showAddDiscounts: false,
                                                                    allowDiscountRemoval: false,
                                                                    showAddTaxId: false,
                                                                },
                                                                transactionId: response.data
                                                            });
                                                    }).catch(error => {
                                                        console.error(error);
                                                    });
                                        }}>Change Payment Method</Button>
                                        <Button color="red" size="sm"
                                                className="w-3/4 self-center mt-5 text-center justify-center"
                                                variant="outlined">Remove Payment Method</Button>

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
                                                    Paddle.Checkout.open({
                                                        settings: {
                                                            displayMode: "inline",
                                                            theme: "light",
                                                            locale: "en",
                                                            showAddDiscounts: false,
                                                            allowDiscountRemoval: false,
                                                            showAddTaxId: false,
                                                        },
                                                        customer: {
                                                            email: auth.user.email
                                                        },
                                                        items: [
                                                            {
                                                                priceId: 'pri_01j17pahhf3d620xya4x9ckrg7',
                                                            }
                                                        ],
                                                    });
                                                }}
                                        >Add payment method</Button>
                                    </CardBody>
                                </Card>)
                            }
                        </div>
                        <div className="p-6">
                            <InvoicesTable></InvoicesTable>
                        </div>
                    </div>
                </div>
            </div>

        </AuthenticatedLayout>
    );
}
