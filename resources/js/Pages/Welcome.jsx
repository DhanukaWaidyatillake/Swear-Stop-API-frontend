import {Link, router} from "@inertiajs/react";
import React, {useRef} from "react";
import ApplicationLogo from "@/Components/ApplicationLogo.jsx";
import TopLeftBackgroundSVG from "@/Background/TopLeftBackgroundSVG.jsx";
import ApiTestForm from "@/Components/LandingPage/ApiTestForm.jsx";
import MiddleLeftBackgroundSVG from "@/Background/MiddleLeftBackgroundSVG.jsx";
import PricingSection from "@/Components/LandingPage/PricingSection.jsx";
import MiddleRightBackgroundSVG from "@/Background/MiddleRightBackgroundSVG.jsx";
import FeatureSection from "@/Components/LandingPage/FeatureSection.jsx";
import mainGif from "../../gif/main.gif";
import {Button} from "@material-tailwind/react";
import ThunderIcon from "@/Icons/ThunderIcon.jsx";
import DoubleArrow from "@/Icons/DoubleArrow.jsx";
import Footer from "@/Components/Footer/Footer.jsx";


let currentIndex = 0;

export default function Welcome({
                                    auth,
                                    profanityCategories,
                                    api_domain,
                                    maxUsage,
                                }) {
    const apiTestFormRef = useRef(null);



    return (
        <div className="relative h-screen bg-white overflow-x-clip ">
            <div className="absolute top-0 right-0 z-0 w-0 lg:w-full  overflow-hidden">
                <TopLeftBackgroundSVG></TopLeftBackgroundSVG>
            </div>

            <nav className="flex justify-between p-5 w-screen ">
                <div className="ml-4">
                    <ApplicationLogo className="w-20 h-16 fill-current"/>
                </div>
                <div className="z-10">
                    {auth.user ? (
                        <Link
                            href={route("dashboard")}
                            className="lg:text-white px-4"
                        >
                            Dashboard
                        </Link>
                    ) : (
                        <>
                            <Link
                                href={route("login")}
                                className="lg:text-white px-3"
                            >
                                Log in
                            </Link>
                            <Link
                                href={route("register")}
                                className="lg:text-white px-3"
                            >
                                Sign Up
                            </Link>
                            <Link
                                href='/docs/1.0/authentication'
                                className="lg:text-white px-3"
                            >
                                Docs
                            </Link>
                        </>
                    )}
                </div>
            </nav>
            <div className="flex justify-between h-5/6 w-full md:flex-col-reverse lg:flex-row">
                <div className="lg:ml-24 lg:mb-16 content-center lg:w-1/2">
                    <div>
                        <h1 className="p-2 text-5xl font-black text-center lg:text-left">
                            Swear-Stop API
                        </h1>
                    </div>
                    <h1 className="p-2 text-xl font-bold text-center lg:text-left mt-10 leading-8">
                        <span className="hidden lg:inline">
                            Enhance your app with our top-tier Text Moderation{" "}
                            <br/>
                            Service! Effortlessly filter explicit words and
                            phrases <br/>
                            to create a safe, inclusive and seamless user
                            experience.
                        </span>
                        <span className="inline lg:hidden">
                            Enhance your app with our top-tier Text Moderation
                            Service! Effortlessly filter explicit words and
                            phrases to create a safe, inclusive and seamless
                            user experience.{" "}
                        </span>
                    </h1>
                    <div className="flex flex-col lg:flex-row mt-10">
                        <div className="flex justify-center lg:justify-start">
                            <Button
                                variant="outlined"
                                size="sm"
                                className="flex items-center gap-3 z-50"
                                onClick={() => {
                                    apiTestFormRef.current.scrollIntoView({
                                        behavior: "smooth",
                                    });
                                }}
                            >
                                See in Action
                                <ThunderIcon></ThunderIcon>
                            </Button>
                        </div>
                        <div className="flex justify-center lg:justify-start lg:ml-5 mt-5 lg:mt-0">
                            <Button
                                size="sm"
                                className="flex items-center gap-3 z-50"
                                onClick={() => {
                                    router.visit("/register");
                                }}
                            >
                                Get Started
                                <DoubleArrow></DoubleArrow>
                            </Button>
                        </div>
                    </div>
                </div>
                <div className="hidden lg:z-10 items-center md:flex h-3/4 lg:h-auto">
                    <div className="sm:scale-[0.4] lg:scale-[0.65] flex justify-center md:justify-start">
                        <img
                            className="rounded-3xl lg:w-4/5 shadow-xl shadow-blue-gray-300/30"
                            src={mainGif}
                            alt="welcome page gif"
                        />
                    </div>
                </div>
            </div>

            <div className="hidden lg:block absolute left-0  z-0 transition scale-y-[4] scale-x-[3]">
                <MiddleLeftBackgroundSVG></MiddleLeftBackgroundSVG>
            </div>

            <br/>

            <div
                ref={apiTestFormRef}
                className="flex items-center flex-col mt-20"
            >
                <h1 className="p-2 text-4xl font-black text-center">
                    See In Action
                </h1>
                <ApiTestForm
                    profanityCategories={profanityCategories}
                    api_domain={api_domain}
                ></ApiTestForm>
            </div>

            <br/>

            <div className="hidden lg:block absolute right-16 z-0 scale-y-[4] scale-x-[3] rotate-180">
                <MiddleRightBackgroundSVG></MiddleRightBackgroundSVG>
            </div>

            <div className="flex items-center flex-col mt-24 ">
                <h1 className="p-2 text-4xl font-black text-center mb-10">
                    Experience Unmatched Reliability <br/> and Control
                </h1>
                <FeatureSection></FeatureSection>
            </div>

            <div className="hidden lg:block absolute left-0  z-0 transition scale-y-[4] scale-x-[3]">
                <MiddleLeftBackgroundSVG></MiddleLeftBackgroundSVG>
            </div>

            <div className="flex items-center flex-col mt-24 mb-20">
                <h1 className="text-4xl font-black text-center p-5">
                    <span className="underline">100</span> Free Requests to Get
                    You Started! <br/> Pay per Usage Afterwards.{" "}
                </h1>
                <PricingSection max_usage={maxUsage}></PricingSection>
                {/*<h1 className="text-4xl font-black text-center mt-20 p-5">*/}
                {/*    <span className="underline">3,726</span> clients count on*/}
                {/*    Swear-Stop for profanity filtering. <br/>*/}
                {/*    Join the movement!{" "}*/}
                {/*</h1>*/}
            </div>
            <Footer></Footer>
        </div>
    );
}
