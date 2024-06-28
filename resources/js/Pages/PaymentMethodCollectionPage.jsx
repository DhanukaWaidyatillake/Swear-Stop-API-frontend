import 'react-toastify/dist/ReactToastify.css'
import {router} from "@inertiajs/react";
import {useEffect} from "react";
import {Button} from "@material-tailwind/react";

export default function PaymentMethodCollectionPage({auth,txn_id}) {

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
                            window.Paddle.Checkout.close()
                            router.post('/card-saved-successfully', data, {
                                'preserveScroll': true
                            })
                            break;
                        case "checkout.error":
                            router.post('/card-saved-failed', data, {
                                'preserveScroll': true
                            })
                            break;
                    }
                }
            });

            if(txn_id){
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
                            priceId: 'pri_01j17pahhf3d620xya4x9ckrg7',
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
