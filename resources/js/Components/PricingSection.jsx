import {Slider} from "@material-tailwind/react";

export default function PricingSection(props) {
    return (
        <div className="text-center flex flex-col items-center mt-20">
            <div className="w-4/5 sm:w-1/2  p-2 border-2 border-black rounded-3xl flex flex-col items-center">
                <h1 className="text-xl font-bold text-center mt-5">
                    Estimate your monthly cost
                </h1>
                <div className="mt-10 w-2/3 ">
                    <Slider defaultValue={50}/>
                    <div className="flex justify-between">
                        <span>0</span>
                        <span>10,000</span>
                    </div>
                    <h1 className="text-xl font-bold text-center mt-5 ">
                        API calls per month → 752
                    </h1>
                    <h1 className="text-xl font-bold text-center mt-5 ">
                        Monthly Cost → $1,342
                    </h1>
                    <h1 className="text-md font-bold text-center mt-10 mb-5 normal-case">
                        Ready to scale beyond 10,000 API calls monthly? <span
                        className="underline">Reach out!</span>
                    </h1>
                </div>
            </div>
        </div>
    );
}
