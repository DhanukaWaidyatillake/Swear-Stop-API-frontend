import {Link, router} from '@inertiajs/react';
import React, {useRef, useState} from 'react';
import ApplicationLogo from "@/Components/ApplicationLogo.jsx";
import TopLeftBackgroundSVG from "@/Background/TopLeftBackgroundSVG.jsx";
import ApiTestForm from "@/Components/LandingPage/ApiTestForm.jsx";
import MiddleLeftBackgroundSVG from "@/Background/MiddleLeftBackgroundSVG.jsx";
import PricingSection from "@/Components/LandingPage/PricingSection.jsx";
import MiddleRightBackgroundSVG from "@/Background/MiddleRightBackgroundSVG.jsx";
import FeatureSection from "@/Components/LandingPage/FeatureSection.jsx";
import mainGif from '../../gif/main.gif';
import {Button, Typography} from "@material-tailwind/react";
import ThunderIcon from "@/Icons/ThunderIcon.jsx";
import DoubleArrow from "@/Icons/DoubleArrow.jsx";

let currentIndex = 0;

export default function Welcome({auth,profanityCategories,api_domain}) {

    const apiTestFormRef = useRef(null);

    return (
        <div className="relative h-screen bg-white overflow-x-clip ">
            <div className="absolute top-0 right-0 z-0 w-11/12  lg:w-auto md:w-4/5 overflow-hidden">
                <TopLeftBackgroundSVG></TopLeftBackgroundSVG>
            </div>

            <nav className="flex justify-between p-5 w-screen ">
                <div className="ml-4">
                    <ApplicationLogo className="w-20 h-16 fill-current"/>
                </div>
                <div className="z-10">
                    {auth.user ? (
                        <Link
                            href={route('dashboard')}
                            className="text-white px-4"
                        >
                            Dashboard
                        </Link>
                    ) : (
                        <>
                            <Link
                                href={route('login')}
                                className="text-white px-3"
                            >
                                Log in
                            </Link>
                            <Link
                                href={route('register')}
                                className="text-white px-3"
                            >
                                Sign Up
                            </Link>
                        </>
                    )}
                </div>
            </nav>
            <div className="flex justify-between h-5/6 w-full">
                <div className="sm:ml-24 sm:mb-16 content-center w-1/2">
                    <div>
                        <h1 className="p-2 text-5xl font-black text-center sm:text-left">Swear-Stop API</h1>
                    </div>
                    <h1 className="p-2 text-xl font-bold text-center sm:text-left mt-10 leading-8">
                        Enhance your app with our top-tier Text Moderation <br/>Service!
                        Effortlessly filter explicit words and phrases <br/> to create a safe, inclusive and seamless
                        user experience.
                    </h1>
                    <div className="flex mt-10">
                        <div className="flex">
                            <Button variant="outlined" size="sm" className="flex items-center gap-3" onClick={() => {
                                apiTestFormRef.current.scrollIntoView({behavior: 'smooth'});
                            }}>
                                See in Action
                                <ThunderIcon></ThunderIcon>
                            </Button>
                        </div>
                        <div className="flex ml-5">
                            <Button size="sm" className="flex items-center gap-3 z-50" onClick={() => {
                                router.visit('/register')
                            }}>
                                Get Started
                                <DoubleArrow></DoubleArrow>
                            </Button>
                        </div>
                    </div>
                </div>
                <div className="hidden z-10 items-center sm:flex">
                    <div className="scale-[0.65] flex justify-start">
                        <img
                            className="rounded-3xl w-4/5 shadow-xl shadow-blue-gray-300/30"
                            src={mainGif}
                            alt="welcome page gif"
                        />
                    </div>
                </div>
            </div>

            <div className="hidden sm:block absolute left-0  z-0 transition scale-y-[4] scale-x-[3]">
                <MiddleLeftBackgroundSVG></MiddleLeftBackgroundSVG>
            </div>

            <br/>

            <div ref={apiTestFormRef} className="flex items-center flex-col mt-20">
                <h1 className="p-2 text-4xl font-black text-center">See In Action...</h1>
                <ApiTestForm profanityCategories={profanityCategories} api_domain={api_domain}></ApiTestForm>
            </div>

            <br/>

            <div className="hidden sm:block absolute right-56 z-0 transition scale-y-[4] scale-x-[3] -translate-y-64">
                <MiddleRightBackgroundSVG></MiddleRightBackgroundSVG>
            </div>

            <div className="flex items-center flex-col mt-24 h-4/5">
                <h1 className="p-2 text-4xl font-black text-center mb-10">Experience Unmatched Reliability <br/> and
                    Control
                </h1>
                <FeatureSection></FeatureSection>
            </div>

            <div className="hidden sm:block absolute left-0  z-0 transition scale-y-[4] scale-x-[3]">
                <MiddleLeftBackgroundSVG></MiddleLeftBackgroundSVG>
            </div>


            <div className="mt-64">
                <h1 className="text-4xl font-black text-center p-5"><span className="underline">100</span> Free Requests
                    to Get You Started! <br/> Pay per Usage Afterwards. </h1>
                <PricingSection></PricingSection>
                <h1 className="text-4xl font-black text-center mt-20 p-5"><span
                    className="underline">3,726</span> clients
                    count on Swear-Stop for profanity filtering. <br/>Join the movement! </h1>
            </div>
            <footer
                className="bg-black mt-20 p-10 flex w-full flex-row flex-wrap items-center justify-center gap-y-6 gap-x-12 border-t border-blue-gray-50 py-6 text-center md:justify-between">
                <Typography color="blue-gray" className="font-normal text-white">
                    &copy; Swear Stop API
                </Typography>
                <ul className="flex flex-wrap items-center gap-y-2 gap-x-8">
                    <li>
                        <Typography
                            as="a"
                            href="#"
                            color="blue-gray"
                            className="font-normal transition-colors hover:text-blue-500 focus:text-blue-500 text-white"
                        >
                            About Us
                        </Typography>
                    </li>
                    <li>
                        <Typography
                            as="a"
                            href="#"
                            color="blue-gray"
                            className="font-normal transition-colors hover:text-blue-500 focus:text-blue-500 text-white"
                        >
                            License
                        </Typography>
                    </li>
                    <li>
                        <Typography
                            as="a"
                            href="#"
                            color="blue-gray"
                            className="font-normal transition-colors hover:text-blue-500 focus:text-blue-500 text-white"
                        >
                            Contribute
                        </Typography>
                    </li>
                    <li>
                        <Typography
                            as="a"
                            href="#"
                            color="blue-gray"
                            className="font-normal transition-colors hover:text-blue-500 focus:text-blue-500 text-white"
                        >
                            Contact Us
                        </Typography>
                    </li>
                </ul>
            </footer>
        </div>
    );
}
