import {Button, Typography} from "@material-tailwind/react";
import 'react-toastify/dist/ReactToastify.css'
import {useEffect, useState} from "react";
import {router} from "@inertiajs/react";

export default function ConfirmCardRemovalPopup({visible, setVisible}) {

    const [pendingAmount, setPendingAmount] = useState(0);

    const loadData = (page) => {
        axios.get('/show_payment_method_removal_popup').then(response => {
            setPendingAmount(response.data);
        }).catch(error => {
            console.error(error);
        });
    }

    useEffect(() => {
        if (visible) {
            loadData(1);
        }
    }, [visible]);

    return (
        <div data-dialog-backdrop="manage-list-dialog" data-dialog-backdrop-close="true" onClick={(event) => {
            if (event.target.getAttribute('data-dialog-backdrop') !== null) {
                setVisible(false)
            }
        }}
             className={`${visible ? '' : 'pointer-events-none'}  fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 ${visible ? 'opacity-100' : 'opacity-0'}  backdrop-blur-sm transition-opacity duration-300`}>

            <div data-dialog="sign-in-dialog"
                 className="relative mx-auto flex w-full max-w-[30rem] flex-col rounded-xl bg-white bg-clip-border text-gray-700 shadow-md">
                <Typography variant="h5" color="blue-gray"
                            className="text-center font-extrabold flex justify-center m-5">
                    <span>Confirm Payment Method Removal</span>
                </Typography>
                <div
                    className="relative p-4 font-sans text-base antialiased font-light leading-relaxed border-t border-b border-t-blue-gray-100 border-b-blue-gray-100 text-black">
                    Your payment method will be removed after the remaining balance of <span
                    className="font-extrabold text-black">${pendingAmount}</span> is charged.
                    To proceed, please confirm to complete the payment and finalize the removal of
                    your card.
                    <br/><br/> <span className="text-red-400">Note that your API access will be revoked once the card is removed.</span>
                </div>
                <div className="flex flex-wrap items-center justify-end p-4 shrink-0 text-blue-gray-500">
                    <Button
                        className="block w-full select-none rounded-lg bg-gradient-to-tr from-gray-900 to-gray-800 py-3 px-6 text-center align-middle font-sans text-xs font-bold uppercase text-white shadow-md shadow-gray-900/10 transition-all hover:shadow-lg hover:shadow-gray-900/20 active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        onClick={() => {
                            router.visit(route('remove-payment-method'), {
                                method: 'post',
                                onFinish: page => {
                                    setVisible(false)
                                    window.location.reload()
                                },
                            })
                        }}
                    >
                        Make Payment and Remove Card
                    </Button>
                </div>
            </div>
        </div>
    );
}
