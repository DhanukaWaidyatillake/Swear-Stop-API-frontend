import {useEffect, useState} from "react";
import {Accordion, AccordionBody, AccordionHeader, Chip} from "@material-tailwind/react";
import MiddleRightBackgroundSVG from "@/Background/MiddleRightBackgroundSVG.jsx";
import RocketIcon from "@/Icons/RocketIcon.jsx";
import ThunderIcon from "@/Icons/ThunderIcon.jsx";
import ListIcon from "@/Icons/ListIcon.jsx";
import AnalyticsIcon from "@/Icons/AnalyticsIcon.jsx";
import MetaDataIcon from "@/Icons/MetaDataIcon.jsx";
import NoSubscriptionIcon from "@/Icons/NoSubscriptionIcon.jsx";

export default function FeatureSection() {

    const [open, setOpen] = useState("1");

    const [isAccordionFocused, setIsAccordionFocused] = useState(false);

    const handleOpen = (value) => {
        setOpen(value)
    };


    const content = {
        1: {
            'header': 'Accurate and Reliable',
            'body': 'Our systems have a 100% uptime and we wouldn\'t let a single explicit word slip through the cracks.',
            'svg': <RocketIcon/>
        },
        2: {
            'header': 'Lighting fast',
            'body': 'Our architecture is highly resilient and scalable, perfectly tailored to meet your needs—whether you\'re a tech giant or an emerging startup.',
            'svg': <ThunderIcon/>
        },
        3: {
            'header': 'Maintain your own blacklist and whitelist',
            'body': 'Use the REST API or your dashboard to manage a list of words that should be white listed or black listed by force.',
            'svg': <ListIcon/>
        },
        4: {
            'header': 'Analytics and Insights',
            'body': 'Gain valuable insights and detailed analytics on your API usage and historical profanity filtering using reports and graphs on our dashboard',
            'svg': <AnalyticsIcon/>
        },
        5: {
            'header': 'Make use of Metadata',
            'body': 'Enhance your analytics by associating metadata with each request and use them for for personalized tracking and deeper monitoring.',
            'svg': <MetaDataIcon/>
        },
        6: {
            'header': 'Get Started For Free!',
            'body': 'No credit card needed to get started! Enjoy your first 100 API calls absolutely free. Upgrade to one of our amazingly cheap subscriptions once you\'re ready.' ,
            'svg': <NoSubscriptionIcon/>
        },
    };

    return (
        <div className="w-3/4 md:w-1/2">
            {Object.keys(content).map(key => (
                <Accordion open={open === key} className="mb-2 rounded-2xl border border-black px-4 mt-5">
                    <AccordionHeader
                        onMouseEnter={() => {
                            setIsAccordionFocused(true)
                            handleOpen(key)
                        }}
                        onMouseLeave={() => {
                            setIsAccordionFocused(false)
                        }}
                        className="font-bold text-blac text-2xl">
                        <div className="flex flex-row justify-between w-full">
                            <div>
                                {content[key].header}
                            </div>
                            <div>
                                {content[key].svg}
                            </div>
                        </div>
                    </AccordionHeader>
                    <AccordionBody className="text-md font-bold text-black">
                        {content[key].body}
                    </AccordionBody>
                </Accordion>
            ))}
        </div>
    );
}
