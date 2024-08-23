import {MagnifyingGlassIcon, PlusIcon} from "@heroicons/react/24/outline";
import {
    Button,
    Card,
    CardBody,
    CardFooter,
    CardHeader,
    Chip,
    Input,
    Switch,
    Typography,
} from "@material-tailwind/react";
import {useEffect, useState} from "react";
import ManageListPopup from "@/Components/ManageLists/ManageListPopup.jsx";
import {Link, router} from "@inertiajs/react";

const TABLE_HEAD = ["Word", "Added on", "Added Through", "Enabled"];


export default function ManageListTable({type}) {

    const [open, setOpen] = useState(false);

    const [data, setData] = useState([]);

    const [currentPage, setCurrentPage] = useState(1)
    const [totalPages, setTotalPages] = useState(1)
    const [searchString, setSearchString] = useState({})
    let prevSearchString = "";


    const loadData = (page) => {
        //Using axios here instead of router since we are not returning an Inertia response

        axios.get(type === "blacklist" ? '/get_blacklisted_words' : '/get_whitelisted_words', {
            params: {
                'search': searchString,
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

    useEffect(() => {
        const handler = setTimeout(() => {
            // console.log(searchString)
            // console.log(prevSearchString)

            if (prevSearchString !== searchString) {
                loadData(1)
                prevSearchString = searchString
            }
        }, 300);

        return () => clearInterval(handler);
    }, [searchString]);


    return (
        <Card className={`h-full w-full ${type === "blacklist" ? '' : 'mt-16'}`}>
            <CardHeader floated={false} shadow={false} className="rounded-none">
                <div className="flex items-center justify-between gap-8">
                    <div>
                        <Typography variant="h5" color="blue-gray">
                            {type === "blacklist" ? 'Blacklisted words' : 'Whitelisted words'}
                        </Typography>
                    </div>
                    <div className="w-full md:w-72">
                        <Input
                            label="Search"
                            icon={<MagnifyingGlassIcon className="h-5 w-5"/>}
                            onChange={(event) => setSearchString({
                                'search_string': event.target.value,
                                'search_field': 'word',
                            })}
                        />
                    </div>
                    <div
                        className={`flex shrink-0 flex-col gap-2 sm:flex-row mr-2`}>
                        <Button
                            className={`flex items-center gap-3 ${type === "blacklist" ? '' : 'bg-white text-black border-black border-2'}`}
                            size="sm" onClick={() => {
                            setOpen(true)
                        }}>
                            <PlusIcon strokeWidth={2} className="h-4 w-4"/> Add Word
                            to {type === "blacklist" ? 'Blacklist' : 'Whitelist'}
                        </Button>
                        <ManageListPopup visible={open} setVisible={setOpen} list_type={type}
                                         newBannedWordAdded={() => loadData(currentPage)}></ManageListPopup>
                    </div>
                </div>
            </CardHeader>
            <CardBody className="overflow-scroll px-0 h-full">
                <table className="mt-4 w-full min-w-max table-auto text-left">
                    <thead>
                    <tr>
                        {TABLE_HEAD.map((head) => (
                            <th
                                className="border-y border-blue-gray-100 bg-blue-gray-50/50 p-4"
                            >
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
                    {data.map(
                        ({id, word, created_at, online, added_through, is_enabled}, index) => {
                            const isLast = index === data.length - 1;
                            const classes = isLast
                                ? "p-4"
                                : "p-4 border-b border-blue-gray-50";

                            return (
                                <tr>
                                    <td className={classes}>
                                        <div className="flex items-center gap-3">
                                            <div className="flex flex-col">
                                                <Typography
                                                    variant="small"
                                                    color="blue-gray"
                                                    className="font-normal"
                                                >
                                                    {word}
                                                </Typography>
                                            </div>
                                        </div>
                                    </td>
                                    <td className={classes}>
                                        <div className="flex flex-col">
                                            <Typography
                                                variant="small"
                                                color="blue-gray"
                                                className="font-normal"
                                            >
                                                {created_at}
                                            </Typography>
                                        </div>
                                    </td>
                                    <td className={classes}>
                                        <div className="w-max">
                                            <Chip
                                                variant="ghost"
                                                size="sm"
                                                value={added_through === "dashboard" ? "Dashboard" : "API"}
                                                color={added_through === "dashboard" ? "green" : "blue"}
                                            />
                                        </div>
                                    </td>
                                    <td className={classes}>
                                        <Switch
                                            key={`${type}-switch-component-${id}`}
                                            ripple={true}
                                            className="h-full w-full checked:bg-black checked:opacity-0"
                                            containerProps={{
                                                className: "w-11 h-6 bg-black",
                                            }}
                                            circleProps={{
                                                className: "before:hidden left-0.5 border-none",
                                            }}
                                            defaultChecked={is_enabled}
                                            onChange={(event) => {
                                                router.put(type === "blacklist" ? '/change_state_blacklist/' + id : '/change_state_whitelist/' + id, {
                                                    'is_enabled': event.target.checked,
                                                },{
                                                    'preserveScroll': true
                                                })
                                            }}
                                        />
                                    </td>
                                </tr>
                            );
                        },
                    )}
                    </tbody>
                </table>
            </CardBody>
            <CardFooter className="flex items-center justify-between border-t border-blue-gray-50 p-4">
                <Typography variant="small" color="blue-gray" className="font-normal">
                    Page {currentPage} of {totalPages}
                </Typography>
                <div className="flex gap-2">
                    <Button variant="outlined" size="sm" disabled={currentPage === 1}
                            onClick={() => loadData(currentPage - 1)}>
                        Previous
                    </Button>
                    <Button variant="outlined" size="sm" disabled={currentPage === totalPages}
                            onClick={() => loadData(currentPage + 1)}>
                        Next
                    </Button>
                </div>
            </CardFooter>
        </Card>
    );
}
