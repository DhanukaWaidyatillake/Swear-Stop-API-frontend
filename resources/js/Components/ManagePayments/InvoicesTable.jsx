import {MagnifyingGlassIcon} from "@heroicons/react/24/outline";
import {
    Button,
    Card,
    CardBody,
    CardFooter,
    CardHeader,
    Chip, IconButton,
    Input,
    Switch,
    Typography,
} from "@material-tailwind/react";
import {PlusIcon} from "@heroicons/react/24/outline/index.js";
import DownloadIcon from "@/Icons/DownloadIcon.jsx";

const TABLE_HEAD = ["Month", "Total Cost", "Invoice Generated At", "Download PDF"];

const TABLE_ROWS = [
    {
        name: "June",
        total_cost: "$54.24",
        org: "Organization",
        online: true,
        date: "23/04/18",
        enabled: true
    },
    {
        name: "July",
        total_cost: "$23.12",
        org: "Developer",
        online: false,
        date: "23/04/18",
        enabled: true
    },
    {
        name: "August",
        total_cost: "$76.11",
        org: "Projects",
        online: false,
        date: "19/09/17",
        enabled: false
    },
    {
        name: "September",
        total_cost: "$11.78",
        org: "Developer",
        online: true,
        date: "24/12/08",
        enabled: false
    },
    {
        name: "October",
        total_cost: "$54.06",
        org: "Executive",
        online: false,
        date: "04/10/21",
        enabled: true
    },
];

export default function InvoicesTable() {
    return (
        <Card className="h-full w-full border-black border-2">
            <CardHeader floated={false} shadow={false} className="rounded-none justify-center text-center self-center">
                <Typography variant="h4" color="blue-gray" className="text-center">
                    Invoices
                </Typography>
            </CardHeader>
            <CardBody className="overflow-scroll px-0">
                <table className="mt-4 w-full min-w-max table-auto text-center">
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
                        ({name, total_cost, online, date, enabled}, index) => {
                            const isLast = index === TABLE_ROWS.length - 1;
                            const classes = isLast
                                ? "p-4 text-center"
                                : "p-4 border-b border-blue-gray-50 text-center";

                            return (
                                <tr key={name} className="text-center">
                                    <td className={classes}>
                                        <div className="flex items-center gap-3 justify-center">
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
                                                {total_cost}
                                            </Typography>
                                        </div>
                                    </td>
                                    <td className="flex flex-col">
                                        <div className="w-max self-center">
                                            <Chip
                                                variant="ghost"
                                                size="sm"
                                                value={"2024-04-23 15:59:02"}
                                                className="text-center justify-center self-center mt-3"
                                            />
                                        </div>
                                    </td>
                                    <td>
                                        <div className="flex justify-center">
                                            <DownloadIcon></DownloadIcon>
                                        </div>
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
