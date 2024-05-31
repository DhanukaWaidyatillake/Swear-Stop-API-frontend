import {MagnifyingGlassIcon, PlusIcon} from "@heroicons/react/24/outline";
import {
    Button,
    Card,
    CardBody,
    CardFooter,
    CardHeader, Checkbox,
    Chip, DialogBody, DialogFooter, DialogHeader,
    Input,
    Switch,
    Typography,
} from "@material-tailwind/react";
import {Dialog} from "@headlessui/react";
import {useState} from "react";
import ManageListPopup from "@/Components/ManageListPopup.jsx";

const TABLE_HEAD = ["Word", "Added on", "Added Through", "Enabled"];

const TABLE_ROWS = [
    {
        name: "Addidas",
        added_on: "2024-08-03",
        org: "Organization",
        online: true,
        date: "23/04/18",
        enabled: true
    },
    {
        name: "Nike",
        added_on: "2024-02-03",
        org: "Developer",
        online: false,
        date: "23/04/18",
        enabled: true
    },
    {
        name: "Puma",
        added_on: "2024-08-01",
        org: "Projects",
        online: false,
        date: "19/09/17",
        enabled: false
    },
    {
        name: "Louis Vitton",
        added_on: "2024-08-09",
        org: "Developer",
        online: true,
        date: "24/12/08",
        enabled: false
    },
    {
        name: "Fila",
        added_on: "2024-03-03",
        org: "Executive",
        online: false,
        date: "04/10/21",
        enabled: true
    },
];

export default function ManageListTable({type}) {

    const [open, setOpen] = useState(false);

    return (
        <Card className={`h-full w-full ${type==="blacklist" ? '' : 'mt-16'}`}>
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
                        />
                    </div>
                    <div
                        className={`flex shrink-0 flex-col gap-2 sm:flex-row mr-2`}>
                        <Button className={`flex items-center gap-3 ${type === "blacklist" ? '' : 'bg-white text-black border-black border-2'}`} size="sm" onClick={() => {
                            setOpen(true)
                        }}>
                            <PlusIcon strokeWidth={2} className="h-4 w-4"/> Add Blacklisted Word
                        </Button>
                        <ManageListPopup visible={open} setVisible={setOpen} list_type={type}></ManageListPopup>
                    </div>
                </div>
            </CardHeader>
            <CardBody className="overflow-scroll px-0">
                <table className="mt-4 w-full min-w-max table-auto text-left">
                    <thead>
                    <tr>
                        {TABLE_HEAD.map((head) => (
                            <th
                                key={head}
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
                    {TABLE_ROWS.map(
                        ({name, added_on, online, date, enabled}, index) => {
                            const isLast = index === TABLE_ROWS.length - 1;
                            const classes = isLast
                                ? "p-4"
                                : "p-4 border-b border-blue-gray-50";

                            return (
                                <tr key={name}>
                                    <td className={classes}>
                                        <div className="flex items-center gap-3">
                                            <div className="flex flex-col">
                                                <Typography
                                                    variant="small"
                                                    color="blue-gray"
                                                    className="font-normal"
                                                >
                                                    {name}
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
                                                {added_on}
                                            </Typography>
                                        </div>
                                    </td>
                                    <td className={classes}>
                                        <div className="w-max">
                                            <Chip
                                                variant="ghost"
                                                size="sm"
                                                value={online ? "API" : "Dashboard"}
                                                color={online ? "green" : "blue"}
                                            />
                                        </div>
                                    </td>
                                    <td className={classes}>
                                        <Switch
                                            id={`blacklist-switch-component-${index}`}
                                            ripple={true}
                                            className="h-full w-full checked:bg-black checked:opacity-0"
                                            containerProps={{
                                                className: "w-11 h-6 bg-black",
                                            }}
                                            circleProps={{
                                                className: "before:hidden left-0.5 border-none",
                                            }}
                                            defaultChecked={enabled}
                                        /></td>
                                </tr>
                            );
                        },
                    )}
                    </tbody>
                </table>
            </CardBody>
            <CardFooter className="flex items-center justify-between border-t border-blue-gray-50 p-4">
                <Typography variant="small" color="blue-gray" className="font-normal">
                    Page 1 of 10
                </Typography>
                <div className="flex gap-2">
                    <Button variant="outlined" size="sm">
                        Previous
                    </Button>
                    <Button variant="outlined" size="sm">
                        Next
                    </Button>
                </div>
            </CardFooter>
        </Card>
    );
}
