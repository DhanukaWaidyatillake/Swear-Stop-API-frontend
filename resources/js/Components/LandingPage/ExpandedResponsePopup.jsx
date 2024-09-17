import {Typography} from "@material-tailwind/react";
import 'react-toastify/dist/ReactToastify.css'
import {useEffect, useState} from "react";
import CodeViewComponent from "@/Components/CodeViewComponent.jsx";

export default function ExpandedResponsePopup({visible, setVisible, json}) {

    return (
        <div data-dialog-backdrop="manage-list-dialog" data-dialog-backdrop-close="true" onClick={(event) => {
            if (event.target.getAttribute('data-dialog-backdrop') !== null) {
                json=null
                setVisible(false)
            }

        }}
             className={`${visible ? '' : 'pointer-events-none'}  fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 ${visible ? 'opacity-100' : 'opacity-0'}  backdrop-blur-sm transition-opacity duration-300`}>

            <div data-dialog="sign-in-dialog"
                 className="relative mx-auto flex w-full max-w-[50rem] flex-col rounded-xl bg-white bg-clip-border text-gray-700 shadow-md min-h-[60vh] justify-center">

                <div className="min-w-[90%] self-center h-fit">
                    <CodeViewComponent json={json} max_height={96}></CodeViewComponent>
                </div>
            </div>
        </div>
    );
}
