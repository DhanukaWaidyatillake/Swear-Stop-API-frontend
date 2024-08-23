import InputLabel from "@/Components/BreezeComponents/InputLabel.jsx";
import TextInput from "@/Components/BreezeComponents/TextInput.jsx";
import {Button, Chip} from "@material-tailwind/react";
import RegenerateIconSVG from "@/Icons/RegenerateIconSVG.jsx";
import TickIconSVG from "@/Icons/TickIconSVG.jsx";
import DownArrowSVG from "@/Icons/DownArrowSVG.jsx";
import {useEffect, useState} from "react";
import VerticalStepper from "@/Components/LandingPage/VerticalStepper.jsx";

export default function ApiTestForm(props) {

    const categories = ['Slang', 'Sexual', 'Recreational Drugs', 'Political and religious', 'Alcohol', 'Weapons and Warfare', 'Gambling', 'Violence', 'All Categories'];

    const [selectedCategories, setSelectedCategories] = useState([]);

    const [isActive, setIsActive] = useState(false);

    const [text, setText] = useState('');

    const toggleCategory = (category) => {
        if (category === 'All Categories') {
            if (selectedCategories.includes('All Categories')) {
                setSelectedCategories([]);
            } else {
                setSelectedCategories(categories);
            }
        } else {
            if (selectedCategories.includes('All Categories')) {
                setSelectedCategories(selectedCategories.splice(selectedCategories.indexOf('All Categories'), 1));
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

    const handleTextChange = (event) => {
        setText(event.target.value);
        console.log(text.length)
    };

    useEffect(() => {
        if (text.length !== 0) {
            if (selectedCategories.length !== 0) {
                setIsActive(true);
            }
        } else {
            setIsActive(false)
        }
    }, [text])


    const [step, setStep] = useState(1);

    const submit = (e) => {
        e.preventDefault();

        post(route('login'));
    };
    return (
        <div className="w-4/5 sm:w-1/2 flex">
            <div>
                <InputLabel htmlFor="email" className="mt-5" value="Type in anything ..."/>
                <div>
                    <TextInput
                        id="sentence"
                        name="sentence"
                        className="mt-3 block w-full"
                        value={text}
                        onChange={handleTextChange}
                    />
                </div>
                <div className="mt-5 flex justify-center">
                    <Button variant="outlined" size="sm" className="flex items-center gap-3" onClick={() => setStep(1)}>
                        Refresh Sentence
                        <RegenerateIconSVG></RegenerateIconSVG>
                    </Button>
                </div>
                <InputLabel className="mt-16 mb-6" htmlFor="chipSelect" value="Select Moderation Categories To Filter"/>
                <div className="mt-4 flex flex-wrap justify-center">
                    {categories.map((item, index) => (
                        <div key={index} className="mb-3">
                            <Chip size="lg"
                                  className={`rounded-full cursor-pointer transition background-color 0.5s ease, color 0.5s ease ml-5 ${selectedCategories.includes(item) ? 'bg-black text-white' : ''}`}
                                  variant="outlined" value={item}
                                  onClick={() => toggleCategory(item)}
                            />
                        </div>
                    ))}
                </div>

                <div className="mt-16 flex justify-center">
                    <Button
                        className={`flex items-center gap-3 ${isActive ? 'text-white' : 'pointer-events-none bg-gray-500'}`}
                        onClick={() => setStep(3)}>
                        Send Request
                        <TickIconSVG></TickIconSVG>
                    </Button>
                </div>
                <div className="mt-5">
                    <div className="bg-black text-white p-4 rounded-xl">
                        <div className="flex justify-between items-center mb-2">
                            <span className="text-gray-400">Response:</span>
                        </div>
                        <div className="overflow-x-auto">
                            <pre id="code" className="text-white max-w-0">
                                <code>
                                    {'{'} <br/>
                                        <span>  </span>"name": "John Doe", <br/>
                                        <span>  </span>"age": 30, <br/>
                                        <span>  </span>"city": "New York", <br/>
                                        <span>  </span>"email": "john.doe@example.com 5tgv345tg45tg5tg345t" <br/>
                                    {'}'}
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>
                <br/>
            </div>
            <div className="mt-20 ml-16 hidden sm:block">
                <VerticalStepper incoming_step={step}></VerticalStepper>
            </div>
        </div>
    );
}
