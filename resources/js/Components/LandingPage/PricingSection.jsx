import {Slider} from "@material-tailwind/react";
import {useEffect, useState} from "react";
import PricingStructurePopup from "@/Components/PricingStructurePopup.jsx";

export default function PricingSection({max_usage}) {

    const [usage, setUsage] = useState(0)
    const [cost, setCost] = useState(0)
    const [openPricingPopup, setOpenPricingPopup] = useState(false);

    const initial_value = 25

    const sendRequest = function (value) {
        axios.get('/calculate-monthly-cost?slider_value=' + value).then(response => {
            console.log(response)
            setUsage(response.data.usage)
            setCost(response.data.cost)
        }).catch(error => {
            console.error(error);
        });
    }

    useEffect(() => {
        sendRequest(initial_value)
    }, []);

    return (
        <div className="text-center flex flex-col items-center mt-20 w-full">
            <div className="w-4/5 sm:w-1/2  p-2 border-2 border-black rounded-3xl flex flex-col items-center">
                <h1 className="text-xl font-bold text-center mt-5">
                    Calculate your monthly cost
                </h1>
                <div className="mt-10 w-2/3 ">
                    <Slider defaultValue={initial_value} onSelect={(event) => {
                        sendRequest(event.target.value)
                        event.target.blur();
                    }}/>
                    <div className="flex justify-between">
                        <span>0</span>
                        <span>{max_usage.toLocaleString('en-US', {
                            maximumFractionDigits: 1
                        })}</span>
                    </div>
                    <h1 className="text-xl font-bold text-center mt-5 ">
                        API calls per month → {usage}
                    </h1>
                    <h1 className="text-xl font-bold text-center mt-5 ">
                        Monthly Cost → ${cost.toLocaleString('en-US', {
                        maximumFractionDigits: 1
                    })}
                    </h1>
                    <a href="#"
                       className="text-sm py-1 pr-2 mb-2 text-center flex justify-center mt-5 transition-transform hover:scale-105 hover:underline"
                       onClick={(event) => {
                           event.preventDefault()
                           setOpenPricingPopup(true)
                       }}>
                        See Pricing Structure
                    </a>
                    <PricingStructurePopup visible={openPricingPopup} setVisible={setOpenPricingPopup}></PricingStructurePopup>
                </div>
            </div>
        </div>
    );
}
