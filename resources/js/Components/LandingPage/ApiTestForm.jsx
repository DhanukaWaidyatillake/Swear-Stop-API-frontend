import InputLabel from "@/Components/BreezeComponents/InputLabel.jsx";
import TextInput from "@/Components/BreezeComponents/TextInput.jsx";
import {Button, Chip, Textarea} from "@material-tailwind/react";
import RegenerateIconSVG from "@/Icons/RegenerateIconSVG.jsx";
import TickIconSVG from "@/Icons/TickIconSVG.jsx";
import DownArrowSVG from "@/Icons/DownArrowSVG.jsx";
import {useEffect, useState} from "react";
import VerticalStepper from "@/Components/LandingPage/VerticalStepper.jsx";
import CodeViewComponent from "@/Components/CodeViewComponent.jsx";

export default function ApiTestForm({profanityCategories, api_domain}) {



    const [selectedCategories, setSelectedCategories] = useState([]);
    const [isActive, setIsActive] = useState(false);
    const [text, setText] = useState('');
    const [sentenceId, setSentenceId] = useState('');
    const [jsonResponse, setJsonResponse] = useState(null);
    const all_category_id = 0;
    const default_json = '{}'

    const toggleCategory = (category) => {
        if (category === all_category_id) {
            if (selectedCategories.includes(all_category_id)) {
                setSelectedCategories([]);
            } else {
                setSelectedCategories(profanityCategories.map(category => category.id));
            }
        } else {
            if (selectedCategories.includes(all_category_id)) {
                setSelectedCategories(selectedCategories.splice(selectedCategories.indexOf(all_category_id), 1));
            }

            if (selectedCategories.includes(category)) {
                setSelectedCategories(selectedCategories.filter((cat) => cat !== category));
            } else {
                setSelectedCategories([...selectedCategories, category]);
            }
        }
    };

    useEffect(() => {
        if (selectedCategories.length === 0) {
            setStep(1)
            setIsActive(false);
        } else {
            setStep(2)
            if (text.length !== 0) {
                setIsActive(true);
            }
        }
    }, [selectedCategories])


    useEffect(() => {
        if (text.length !== 0) {
            if (selectedCategories.length !== 0) {
                setIsActive(true);
            }
        } else {
            setIsActive(false)
        }
    }, [text])


    //loading a random sentence when page loads
    useEffect(() => {
        refreshSentence();
    }, []);

    const refreshSentence = function () {
        axios.get('/api-tester-get-sentence').then(response => {
            setText(response.data.sentence.sentence)
            setSentenceId(response.data.sentence.id)
        }).catch(error => {
            console.error(error);
        });
    }

    const sendRequest = function () {
        setIsActive(false);
        setJsonResponse(null)
        axios.post(api_domain + '/api/text-filter-tester', {
            sentenceId: sentenceId,
            categories: selectedCategories
        }).then(response => {
            setJsonResponse(response.data)
            setStep(3)
            setIsActive(true);
        }).catch(error => {
            console.error(error);
            setIsActive(true);
        });
    }


    const [step, setStep] = useState(1);


    return (
        <div className="w-4/5 sm:w-1/2 flex">
            <div>
                <InputLabel htmlFor="email" className="mt-5" value="Select Text"/>
                <div>
                    <Textarea
                        id="sentence"
                        name="sentence"
                        className="mt-3 block w-full !font-bold !text-lg bg-white !border !border-black rounded-xl disabled:bg-white disabled:text-black "
                        value={text}
                        variant="outlined"
                        size="lg"
                        disabled
                    />
                </div>
                <div className="mt-5 flex justify-center">
                    <Button variant="outlined" size="sm" className="flex items-center gap-3"
                            onClick={() => refreshSentence()}>
                        Refresh Sentence
                        <RegenerateIconSVG></RegenerateIconSVG>
                    </Button>
                </div>
                <InputLabel className="mt-10 mb-6" htmlFor="chipSelect" value="Select Moderation Categories To Filter"/>
                <div className="mt-4 flex flex-wrap justify-center">
                    {profanityCategories.map(({id, profanity_category_name}, index) => (
                        <div key={index} className="mb-3">
                            <Chip size="lg"
                                  className={`rounded-full cursor-pointer transition background-color 0.5s ease, color 0.5s ease ml-5 ${selectedCategories.includes(id) ? 'bg-black text-white' : ''}`}
                                  variant="outlined" value={profanity_category_name}
                                  onClick={() => toggleCategory(id)}
                            />
                        </div>
                    ))}
                </div>

                <div className="mt-8 flex justify-center">
                    <Button
                        className={`flex items-center gap-3 ${isActive ? 'text-white' : 'pointer-events-none bg-gray-500'}`}
                        onClick={() => sendRequest()}>
                        Send Request
                        <TickIconSVG></TickIconSVG>
                    </Button>
                </div>
                <InputLabel className=" mt-6" htmlFor="chipSelect" value="Response "/>
                <CodeViewComponent
                    json={jsonResponse}></CodeViewComponent>
                <br/>
            </div>
            <div className="mt-20 ml-16 hidden sm:block">
                <VerticalStepper incoming_step={step}></VerticalStepper>
            </div>
        </div>
    );
}
