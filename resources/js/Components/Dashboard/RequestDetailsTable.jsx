import {
    Button,
    Card,
    CardBody,
    CardFooter,
    CardHeader,
    Chip,
    Tab,
    Tabs,
    TabsHeader,
    Typography,
} from "@material-tailwind/react";
import {useEffect, useState} from "react";
import RequestDetailsPopup from "@/Components/Dashboard/RequestDetailsPopup.jsx";
import defaultImg from "../../../images/request_details_table_default.png";


const TABS = [
    {
        label: "All",
        value: "all",
    },
    {
        label: "Profanity Detected",
        value: "banned_words_caught",
    },
];

const TABLE_HEAD = ["Text", "Profanity detected", "Timestamp", ""];

export default function RequestDetailsTable(props) {

    const [data, setData] = useState([]);
    const [currentPage, setCurrentPage] = useState(1)
    const [totalPages, setTotalPages] = useState(1)
    const [isProfanityOnlySelected, setIsProfanityOnlySelected] = useState(false)
    const [isOpenRequestDetailsPopup, setIsOpenRequestDetailsPopup] = useState(false)
    const [requestDetailsId, setRequestDetailsId] = useState(0)

    const openRequestDetailsPopup = (request_id) => {
        setRequestDetailsId(request_id)
        setIsOpenRequestDetailsPopup(true)
    }

    const loadData = (page, profanity_detected_only = 0) => {
        //Using axios here instead of router since we are not returning an Inertia response

        axios.get('/load-profanity-history?profanity_detected_only=' + profanity_detected_only, {
            params: {
                'page': page
            }
        }).then(response => {
            setData(response.data.data)
            setCurrentPage(response.data.current_page)
            setTotalPages(response.data.last_page)
        }).catch(error => {
            console.error(error);
        });
    }

    useEffect(() => {
        loadData(1);
    }, []);


    return (
        <Card className="h-full w-full max-h-fit">
            <RequestDetailsPopup visible={isOpenRequestDetailsPopup}
                                 setVisible={setIsOpenRequestDetailsPopup}
                                 requestId={requestDetailsId}></RequestDetailsPopup>
            <CardHeader floated={false} shadow={false} className="rounded-none">
                <div className="mb-8 flex items-center justify-between gap-8">
                    <div>
                        <Typography variant="h5" color="blue-gray">
                            Profanity Filtering History
                        </Typography>
                    </div>

                </div>
                <Tabs value="all" className="w-full md:w-max">
                    <TabsHeader>
                        {TABS.map(({label, value}) => (
                            <Tab key={value} value={value} className="w-max" onClick={() => {
                                setIsProfanityOnlySelected(value === "banned_words_caught")
                                loadData(1, value === "banned_words_caught" ? 1 : 0);
                            }}>
                                &nbsp;&nbsp;{label}&nbsp;&nbsp;
                            </Tab>
                        ))}
                    </TabsHeader>
                </Tabs>
            </CardHeader>
            <CardBody className="h-[50vh] w-full overflow-y-auto">
                {data.length !== 0 ? (
                    <table className="w-full min-w-max table-auto text-left">
                        <thead>
                        <tr>
                            {TABLE_HEAD.map((head) => (
                                <th key={head} className="border-b border-blue-gray-100 bg-blue-gray-50 p-4">
                                    <Typography
                                        variant="small"
                                        color="blue-gray"
                                        className="font-normal leading-none opacity-70"
                                    >
                                        {head}
                                    </Typography>
                                </th>
                            ))}
                        </tr>
                        </thead>
                        <tbody>
                        {data.map(({id, request_body, profanity_caught, created_at}, index) => {
                            const isLast = index === data.length - 1;
                            const classes = isLast ? "p-4" : "p-4 border-b border-blue-gray-50";

                            return (
                                <tr key={request_body}>
                                    <td className={`${classes}`}>
                                        <Typography variant="small" color="blue-gray" className="font-normal max-w-64">
                                            {JSON.parse(request_body).sentence}
                                        </Typography>
                                    </td>
                                    <td className={`${classes} bg-blue-gray-50/50`}>
                                        <Typography variant="small" color="blue-gray" className="font-normal flex">
                                            {profanity_caught && profanity_caught.split(',').map((item, index) => {
                                                return <Chip
                                                    variant="ghost"
                                                    size="sm"
                                                    value={item}
                                                    color="grey"
                                                    className="ml-1"
                                                />
                                            })}
                                        </Typography>
                                    </td>
                                    <td className={classes}>
                                        <Typography variant="small" color="blue-gray" className="font-normal">
                                            {created_at}
                                        </Typography>
                                    </td>
                                    <td className={`${classes} bg-blue-gray-50/50 text-center`}>
                                        <Typography as="a" href="#" variant="small" color="blue-gray"
                                                    className="font-medium" onClick={(event) => {
                                            event.preventDefault()
                                            openRequestDetailsPopup(id)
                                        }}>
                                            Show Details
                                        </Typography>
                                    </td>
                                </tr>
                            );
                        })}
                        </tbody>
                    </table>
                ) : (
                    <div className=" justify-center w-full h-fit">
                        <img
                            className="rounded-3xl opacity-50  shadow-xl shadow-blue-gray-300/30  max-h-[44vh] w-full filter blur-sm"
                            src={defaultImg}
                            alt="default view for request details table"
                        />
                        <div
                            data-dialog="sign-in-dialog"
                            className="h-1/4 absolute inset-64 flex justify-center items-center mx-auto max-w-[24rem] flex-col rounded-xl bg-white bg-clip-border shadow-2xl">
                            <div className="w-2/3 text-center">
                                <Typography color="blue-gray" className="font-bold" variant="h5">
                                    Start Using the Swear Stop API to view your Profanity Filtering history!
                                </Typography>

                            </div>
                        </div>
                    </div>
                )}

            </CardBody>
            <CardFooter className="flex items-center justify-between border-t border-blue-gray-50 p-4">
                <Button variant="outlined" size="sm" disabled={currentPage === 1} id={"prev-button"}
                        onClick={function () {
                            loadData(currentPage - 1, isProfanityOnlySelected ? 1 : 0)
                        }}>
                    Previous
                </Button>
                <Button variant="outlined" size="sm" disabled={currentPage === totalPages} id={"next-button"}
                        onClick={function () {
                            loadData(currentPage + 1, isProfanityOnlySelected ? 1 : 0)
                        }}>
                    Next
                </Button>
            </CardFooter>
        </Card>
    );
}
