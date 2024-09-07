import {Typography} from "@material-tailwind/react";
import 'react-toastify/dist/ReactToastify.css'
import {useEffect, useState} from "react";
import CodeViewComponent from "@/Components/CodeViewComponent.jsx";

export default function RequestDetailsPopup({visible, setVisible, requestId}) {

    const TABLE_HEAD = ["Number of API calls per month", "Price per API call"];

    const [data, setData] = useState([]);

    const loadData = (page) => {
        //Using axios here instead of router since we are not returning an Inertia response
        axios.get('/load-request-details-popup?request_id=' + requestId).then(response => {
            setData(response.data)
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
                setData([])
                setVisible(false)
            }
        }}
             className={`${visible ? '' : 'pointer-events-none'}  fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 ${visible ? 'opacity-100' : 'opacity-0'}  backdrop-blur-sm transition-opacity duration-300`}>

            <div data-dialog="sign-in-dialog"
                 className="relative mx-auto flex w-full max-w-[50rem] flex-col rounded-xl bg-white bg-clip-border text-gray-700 shadow-md">
                <Typography variant="h5" color="blue-gray"
                            className="text-center font-extrabold flex justify-center m-5">
                    <span className="w-1/2">Request Details</span>
                </Typography>
                <div className="w-4/5 self-center">
                    <Typography variant="h6" color="blue-gray"
                                className="mb-5">
                        <span className="w-4/5 ">Request id : <span
                            className="font-light">{data.id}</span></span>
                    </Typography>
                </div>
                <div className="w-4/5 self-center">
                    <Typography variant="h6" color="blue-gray"
                                className="mb-5">
                        <span className="w-4/5 ">Received at : <span
                            className="font-light">{data.created_at}</span></span>
                    </Typography>
                </div>
                <div className="w-4/5 self-center">
                    <CodeViewComponent
                        json={data.request_body ? JSON.parse(data.request_body) : null} title="Request Body"></CodeViewComponent>
                </div>
                <div className="w-4/5 self-center mb-10">
                    <CodeViewComponent
                        json={data.response_body ? JSON.parse(data.response_body) : null} title="Response Body"></CodeViewComponent>                </div>
            </div>
        </div>
    );
}
