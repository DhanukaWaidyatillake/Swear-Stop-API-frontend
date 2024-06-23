import {Alert, Button} from "@material-tailwind/react";
import {useForm} from "@inertiajs/react";
import {useState} from "react";
import {toast} from "react-toastify";

export default function AlertPopup({text}) {
    const [open, setOpen] = useState(true);

    return (
        <Alert
            open={open}
            onClose={() => {
                toast.dismiss()
                setOpen(false)
                toast.clearWaitingQueue();
            }}
            animate={{
                mount: {y: 0},
                unmount: {y: -50},
            }}>
            {text}
        </Alert>
    );
}
