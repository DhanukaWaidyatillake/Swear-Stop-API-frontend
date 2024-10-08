import 'react-toastify/dist/ReactToastify.css'
import {useEffect, useRef} from "react";

export default function PoliciesAndTermsPopup({visible, setVisible, data}) {
    const scrollableDivRef = useRef(null);

    // Scroll to top of popup upon visibility change
    useEffect(() => {
        if (scrollableDivRef.current) {
            scrollableDivRef.current.scrollTop = 0;
        }
    }, [visible]);


    const renderContent = (text) => {
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        const emailRegex = /([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/g;

        // Split text based on URLs, emails, and phone numbers
        const splitText = text.split(/(https?:\/\/[^\s]+|[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}|(\+?\d{1,4}[\s.-]?\(?\d+\)?[\s.-]?\d+[\s.-]?\d+))/g);

        return splitText.map((part, index) => {
            if (urlRegex.test(part)) {
                return (
                    <a key={index} href={part} target="_blank" rel="noopener noreferrer"
                       className="text-blue-600 underline">
                        {part}
                    </a>
                );
            }
            if (emailRegex.test(part)) {
                return (
                    <a key={index} href={`mailto:${part}`} className="text-blue-600 underline">
                        {part}
                    </a>
                );
            }
            return <span key={index}>{part}</span>;  // Regular text
        });
    };


    return (
        <div data-dialog-backdrop="manage-list-dialog" data-dialog-backdrop-close="true" onClick={(event) => {
            if (event.target.getAttribute('data-dialog-backdrop') !== null) {
                setVisible(false)
            }
        }}
             className={`${visible ? '' : 'pointer-events-none'}  fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 ${visible ? 'opacity-100' : 'opacity-0'}  backdrop-blur-sm transition-opacity duration-300`}>

            <div data-dialog="sign-in-dialog"
                 className="relative mx-auto flex w-3/4 lg:w-full max-w-[60rem] flex-col rounded-xl bg-white bg-clip-border text-gray-700 shadow-md h-[70vh] justify-center">

                <div className="min-w-[90%] self-center h-fit overflow-y-auto mt-5 mb-2" ref={scrollableDivRef}>
                    <div className="ml-7 mr-4 rounded-md">
                        <span className="text-3xl font-bold">{data.title}</span> <br/>
                        <div className="pt-2">
                            <span className="font-bold">Effective Date </span>: {data.effectiveDate} <br/>
                            <span className="font-bold">Last Updated </span>: {data.lastUpdated}
                        </div>
                        <br/>

                        {data.sections.map(({title, content}, index) => (
                            <div key={`section-${index}`}
                                 className={`text-left mt-3 ${index === data.sections.length - 1 ? 'pb-7' : ''}`}>
                                <h2 className="font-bold uppercase">{String(index + 1) + ") "}{renderContent(title)}</h2>

                                <ul className="pl-5 pb-1">
                                    {content.map((item, subIndex) => (
                                        <li key={`item-${index}-${subIndex}`} className="pb-1 pt-2">
                                            {typeof item === "string" ? (
                                                // Simple string content
                                                <p><span className="font-bold text-xl">•</span> {renderContent(item)}
                                                </p>
                                            ) : (
                                                Array.isArray(item) ? (
                                                    <div key={`array-${index}-${subIndex}`} className="pl-5">
                                                        {item.map((detail, detailIndex) => (
                                                            <p key={`detail-${index}-${subIndex}-${detailIndex}`}><span
                                                                className="font-bold pr-1">*</span>{renderContent(detail)}
                                                            </p>
                                                        ))}
                                                    </div>
                                                ) : (
                                                    // Nested content (e.g., details within a subsection)
                                                    <div key={`nested-${index}-${subIndex}`}>
                                                        <h4 className="font-semibold">{item.subTitle}</h4>
                                                        <ul>
                                                            {item.details.map((detail, detailIndex) => (
                                                                <li key={`nested-detail-${index}-${subIndex}-${detailIndex}`}
                                                                    className="pl-5">
                                                                    <span
                                                                        className="font-bold text-xl">•</span> {renderContent(detail)}
                                                                </li>
                                                            ))}
                                                        </ul>
                                                    </div>
                                                )
                                            )}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
