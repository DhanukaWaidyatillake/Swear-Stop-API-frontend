import {useState} from "react";
import 'react-toastify/dist/ReactToastify.css'
import {Typography} from "@material-tailwind/react";
import privacy_policy from "../../../data/privacypolicy.json";
import terms_and_conditions from "../../../data/termsandconditions.json";
import PoliciesAndTermsPopup from "@/Components/Footer/PoliciesAndTermsPopup.jsx";
import SupportPopup from "@/Components/Footer/SupportPopup.jsx";


export default function Footer({flash, errors}) {
    let current_content

    if ((window.location.pathname === "/privacy-policy")) {
        current_content = privacy_policy
    } else {
        current_content = terms_and_conditions
    }

    const [isOpenTermsAndPrivacyPopup, setIsOpenTermsAndPrivacyPopup] = useState((window.location.pathname === "/privacy-policy" || (window.location.pathname === "/terms-of-use")))
    const [isOpenSupportPopup, setIsOpenSupportPopup] = useState(false)
    const [termsAndPolicy, setTermsAndPolicy] = useState(current_content)

    return (
        <footer
            className="bg-black p-10 flex w-full flex-row flex-wrap items-center justify-center gap-y-6 gap-x-12 border-t border-blue-gray-50 py-6 text-center md:justify-between">
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
                        onClick={(event) => {
                            event.preventDefault()
                            setIsOpenSupportPopup(true)
                        }}>
                        Support
                    </Typography>
                </li>
            </ul>
            <PoliciesAndTermsPopup visible={isOpenTermsAndPrivacyPopup} setVisible={setIsOpenTermsAndPrivacyPopup}
                                   data={termsAndPolicy}></PoliciesAndTermsPopup>
            <SupportPopup visible={isOpenSupportPopup} setVisible={setIsOpenSupportPopup}></SupportPopup>
        </footer>

    );
}
