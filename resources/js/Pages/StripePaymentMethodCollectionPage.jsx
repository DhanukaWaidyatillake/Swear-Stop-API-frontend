import React, {useEffect, useState} from 'react';
import 'react-toastify/dist/ReactToastify.css';
import {Button} from "@material-tailwind/react";
import {router} from "@inertiajs/react";

export default function StripePaymentMethodCollectionPage({stripe_publishable_key, client_secret}) {

    let stripe = null;
    let elements = null
    let paymentElement = null

    const appearance = {
        theme: 'flat',
        variables: {colorPrimaryText: '#262626'}
    };

    const options = {
        layout: {
            type: 'accordion',
            defaultCollapsed: false,
            radios: false,
            spacedAccordionItems: false
        }
    };

    // Create script to load Stripe.js
    const script = document.createElement('script');
    script.src = 'https://js.stripe.com/v3/';
    script.onload = () => {
        // Initialize Stripe and Elements once the script is loaded
        stripe = window.Stripe(stripe_publishable_key); // Use window.Stripe after script loads
        elements = stripe.elements({clientSecret: client_secret, appearance: appearance});
        paymentElement = elements.create('payment', options);
        paymentElement.mount('#payment-element');
    };
    document.body.appendChild(script);


    return (
        <div className="flex justify-center h-screen items-center w-full flex-col">
            <div id="payment-element" className="w-1/2">

            </div>
            <Button id="card-button" data-secret={client_secret} className="mt-5" onClick={() => {
                if (!stripe) {
                    console.error("Stripe is not initialized yet.");
                    return;
                }
                stripe.confirmSetup({
                    elements,
                    redirect: 'if_required'
                }).then(function (result) {
                    if (result.error) {
                        // Inform the customer that there was an error.
                        router.post('/card-saved-failed', result, {
                            'preserveScroll': true
                        })
                    } else {
                        router.post('/card-saved-successfully', result, {
                            'preserveScroll': true
                        })
                    }
                    // router.get('/payments');
                });
            }}>
                Save Payment Method
            </Button>
            <Button className="mt-5" onClick={() => {
                router.visit(route('payments'))
            }}>
                ← Go Back
            </Button>
        </div>
    );
}
