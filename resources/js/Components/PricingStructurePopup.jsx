import {Typography} from "@material-tailwind/react";
import 'react-toastify/dist/ReactToastify.css'
import {useEffect, useState} from "react";

export default function PricingStructurePopup({visible, setVisible}) {

    const TABLE_HEAD = ["Number of API calls per month", "Price per API call"];

    const [data, setData] = useState([]);

    const loadData = (page) => {
        //Using axios here instead of router since we are not returning an Inertia response
        axios.get('/get_pricing_structure').then(response => {
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
                setVisible(false)
            }
        }}
             className={`${visible ? '' : 'pointer-events-none'}  fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 ${visible ? 'opacity-100' : 'opacity-0'}  backdrop-blur-sm transition-opacity duration-300`}>

            <div data-dialog="sign-in-dialog"
                 className="relative mx-auto flex w-3/4 lg:w-full max-w-[50rem] flex-col rounded-xl bg-white bg-clip-border text-gray-700 shadow-md">
                <Typography variant="h5" color="blue-gray"
                            className="text-center font-extrabold flex justify-center m-5">
                    <span className="w-1/2">Pricing Structure</span>
                </Typography>
                <table
                    className="w-5/6 self-center table-auto border-collapse border border-gray-200 rounded-lg mb-10 ">
                    <thead>
                    <tr>
                        {TABLE_HEAD.map((head) => (
                            <th
                                key={head}
                                className="border-b border-blue-gray-100 bg-blue-gray-50 p-4 text-center">
                                <Typography
                                    variant="small"
                                    color="blue-gray"
                                    className="leading-none opacity-70 font-bold"
                                >
                                    {head}
                                </Typography>
                            </th>
                        ))}
                    </tr>
                    </thead>
                    <tbody>
                    {data.map(({from, to, price_per_api_call}, index) => {
                        const isLast = index === data.length - 1;
                        const classes = isLast ? "p-4" : "p-4 border-b border-blue-gray-50";

                        return (
                            <tr key={name}>
                                <td className={classes}>
                                    <Typography
                                        variant="small"
                                        color="blue-gray"
                                        className="font-normal text-center"
                                    >
                                        {from}  - {isLast ? '∞' : to}
                                    </Typography>
                                </td>
                                <td className={classes}>
                                    <Typography
                                        variant="small"
                                        color="blue-gray"
                                        className="font-normal text-center"
                                    >
                                        ${price_per_api_call}

                                    </Typography>
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
