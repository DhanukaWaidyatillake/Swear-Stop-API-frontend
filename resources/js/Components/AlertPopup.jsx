import {useEffect} from "react";
import {toast} from "react-toastify";
import 'react-toastify/dist/ReactToastify.css'


export default function AlertPopup({flash, errors}) {

    useEffect(() => {

        const showToasts = () => {
            console.log(flash)
            console.log(errors)
            if (flash.message) {
                if (flash.message.type === "success") {
                    toast.success(flash.message.message)
                } else if (flash.message.type === "success") {
                    toast.error(flash.message.message)
                }
            }
            if (errors) {
                console.log(errors)
                toast.error(errors.word || 'Something went wrong')
            }
        };

        showToasts();
    }, [flash, errors]);

    return null;
}
