import {useEffect, useState} from "react";

export default function VerticalStepper({incoming_step}) {

    const [line_1_length, setLine1Length] = useState('0rem');
    const [line_2_length, setLine2Length] = useState('0rem');

    const [dot_1_color, setDot1Color] = useState('300');
    const [dot_2_color, setDot2Color] = useState('300');
    const [dot_3_color, setDot3Color] = useState('300');

    const animationDuration = 500;

    useEffect(() => {
        if (incoming_step === 1) {
            setLine1Length('0rem')
            setLine2Length('0rem')

            setDot1Color('900')
            setDot2Color('300')
            setDot3Color('300')
        }
        if (incoming_step === 2) {
            setLine1Length('14rem')
            setLine2Length('0rem')

            setDot1Color('900')
            setDot2Color('900')
            setDot3Color('300')
        }
        if (incoming_step === 3) {
            setLine1Length('14rem')
            setLine2Length('14rem')

            setDot1Color('900')
            setDot2Color('900')
            setDot3Color('900')
        }
    }, [incoming_step]);

    return (
        <div className="w-1/5 h-4/5">
            <div className="flex flex-col items-center justify-between w-full h-full">
                <div
                    className={`relative z-10 grid w-6 h-6 font-bold text-white transition-all duration-${animationDuration} bg-gray-${dot_1_color} rounded-full place-items-center`}>
                    1
                </div>

                {/*<div className="flex flex-row justify-end">*/}
                {/*    <div*/}
                {/*        className={` relative z-10 grid w-6 h-6 font-bold text-white transition-all duration-${animationDuration} bg-gray-${dot_1_color} rounded-full place-items-center`}>*/}
                {/*        1*/}
                {/*    </div>*/}
                {/*    <div className="text-right w-1/3 ml-1">*/}
                {/*           Refresh*/}
                {/*    </div>*/}
                {/*</div>*/}


                <div className="flex justify-center h-1/2 w-1/2">
                    <div className={`border-r border-gray-${animationDuration} h-full`}></div>
                    <div
                        className={`border-r border-gray-900 h-[${line_1_length}] transition-all duration-${animationDuration}`}></div>
                </div>

                <div
                    className={`relative z-10 grid w-6 h-6 font-bold text-white transition-all duration-${animationDuration} bg-gray-${dot_2_color} rounded-full place-items-center`}>
                    2
                </div>


                <div className="flex justify-center h-1/2 w-full">
                    <div className={`border-r border-gray-${animationDuration} h-full`}></div>
                    <div
                        className={`border-r border-gray-900 h-[${line_2_length}] transition-all duration-${animationDuration}`}></div>
                </div>

                <div
                    className={`relative z-10 grid w-6 h-6 text-white font-bold  transition-all duration-${animationDuration} bg-gray-${dot_3_color} rounded-full place-items-center`}>
                    3
                </div>
            </div>
        </div>
    );
}
