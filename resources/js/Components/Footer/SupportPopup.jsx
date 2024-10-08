
export default function SupportPopup({visible, setVisible}) {


    return (
        <div data-dialog-backdrop="manage-list-dialog" data-dialog-backdrop-close="true" onClick={(event) => {
            if (event.target.getAttribute('data-dialog-backdrop') !== null) {
                setVisible(false)
            }
        }}
             className={`${visible ? '' : 'pointer-events-none'}  fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 ${visible ? 'opacity-100' : 'opacity-0'}  backdrop-blur-sm transition-opacity duration-300`}>

            <div data-dialog="sign-in-dialog"
                 className="relative mx-auto flex lg:w-full max-w-[30rem] flex-col rounded-xl bg-white bg-clip-border text-gray-700 shadow-md h-[30vh] justify-center p-5">
                <h1 className="text-2xl font-bold">We’re here to help! </h1>
                <br/>
                <h1>
                    If you have any questions or need assistance, don’t hesitate to get in touch with our friendly
                    support team at
                    <span> </span>
                    <a href={`mailto:support@swear-stop.com`} className="text-blue-600 underline">
                        support@swear-stop.com
                    </a>
                    <span>. </span>
                    We’re always ready to lend a hand and ensure you have the best experience possible!
                </h1>
            </div>
        </div>
    );
}
