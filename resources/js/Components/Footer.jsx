import {useState} from "react";
import 'react-toastify/dist/ReactToastify.css'
import {Typography} from "@material-tailwind/react";
import privacy_policy from "../../data/privacypolicy.json";
import terms_and_conditions from "../../data/termsandconditions.json";
import PoliciesAndTermsPopup from "@/Components/WelcomePageComponents/PoliciesAndTermsPopup.jsx";


export default function Footer({flash, errors}) {
    const [isOpenTermsAndPrivacyPopup, setIsOpenTermsAndPrivacyPopup] = useState(false)
    const [termsAndPolicy, setTermsAndPolicy] = useState(terms_and_conditions)

    return (
        <footer
            className="bg-black mt-20 p-10 flex w-full flex-row flex-wrap items-center justify-center gap-y-6 gap-x-12 border-t border-blue-gray-50 py-6 text-center md:justify-between">
            <Typography
                color="blue-gray"
                className="font-normal text-white"
            >
                &copy; Swear Stop API
            </Typography>
            <ul className="flex flex-wrap items-center gap-y-2 gap-x-8">
                <li>
                    <Typography
                        as="a"
                        href="#"
                        color="blue-gray"
                        className="font-normal transition-colors text-white"
                        onClick={(event) => {
                            event.preventDefault()
                            setTermsAndPolicy(privacy_policy)
                            setIsOpenTermsAndPrivacyPopup(true)
                        }}
                    >
                        Privacy Policy
                    </Typography>
                </li>
                <li>
                    <Typography
                        as="a"
                        href="#"
                        color="blue-gray"
                        className="font-normal transition-colors text-white"
                        onClick={(event) => {
                            event.preventDefault()
                            setTermsAndPolicy(terms_and_conditions)
                            setIsOpenTermsAndPrivacyPopup(true)
                        }}>
                        Terms of Use
                    </Typography>
                </li>
                <li>
                    <Typography
                        as="a"
                        href="#"
                        color="blue-gray"
                        className="font-normal transition-colors text-white"
                    >
                        Support
                    </Typography>
                </li>
            </ul>
            <PoliciesAndTermsPopup visible={isOpenTermsAndPrivacyPopup} setVisible={setIsOpenTermsAndPrivacyPopup}
                                   data={termsAndPolicy}></PoliciesAndTermsPopup>
        </footer>

    );
}
