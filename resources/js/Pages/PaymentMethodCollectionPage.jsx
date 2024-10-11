import 'react-toastify/dist/ReactToastify.css'
import {router} from "@inertiajs/react";
import {useEffect} from "react";
import {Button} from "@material-tailwind/react";

export default function PaymentMethodCollectionPage({auth, txn_id, price_id}) {

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

                            setTimeout(function () {
                                window.Paddle.Checkout.close()
                                router.get('/payments');
                            }, 2000);  // 5000 milliseconds = 5 seconds
                            break;
                        case "checkout.error":
                            router.post('/card-saved-failed', data, {
                                'preserveScroll': true
                            })

                            setTimeout(function () {
                                window.Paddle.Checkout.close()
                                router.get('/payments');
                            }, 2000);  // 5000 milliseconds = 5 seconds
                            break;
                    }
                }
            });

            if (txn_id) {
                window.Paddle.Checkout.open({
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
                    transactionId: txn_id
                });
            } else {
                window.Paddle.Checkout.open({
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
                            priceId: price_id,
                        }
                    ],
                });
            }
        }
    };

    let visible = true;

    return (
        <div>
            <div className='flex justify-center'>
                <Button className="mt-[70vh] flex z-[9999999999999]" onClick={() => {
                    window.Paddle.Checkout.close()
                    router.visit(route('payments'))
                }}>
                    ← Go Back
                </Button>
            </div>
        </div>
    );
}
