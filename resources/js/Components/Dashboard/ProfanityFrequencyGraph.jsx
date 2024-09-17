import {
    Card,
    CardBody,
    CardHeader,
    Typography,
} from "@material-tailwind/react";
import Chart from "react-apexcharts";
import Dropdown from "@/Components/BreezeComponents/Dropdown.jsx";
import ChartTimeframeSelector from "@/Components/Dashboard/ChartTimeframeSelector.jsx";
import {useState} from "react";
import {EnvelopeOpenIcon} from "@heroicons/react/24/outline/index.js";

export default function ProfanityFrequencyGraph() {

    const [xAxis, setXAxis] = useState([]);
    const [yAxis, setYAxis] = useState([]);

    const chartConfig = {
        type: "bar",
        height: 240,
        series: [
            {
                name: "Sales",
                data: yAxis,
            },
        ],
        options: {
            chart: {
                toolbar: {
                    show: false,
                },
            },
            title: {
                show: "",
            },
            dataLabels: {
                enabled: false,
            },
            colors: ["#020617"],
            plotOptions: {
                bar: {
                    columnWidth: "40%",
                    borderRadius: 2,
                },
            },
            xaxis: {
                axisTicks: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
                labels: {
                    style: {
                        colors: "#616161",
                        fontSize: "12px",
                        fontFamily: "inherit",
                        fontWeight: 400,
                    },
                },
                categories: xAxis,
            },
            yaxis: {
                labels: {
                    style: {
                        colors: "#616161",
                        fontSize: "12px",
                        fontFamily: "inherit",
                        fontWeight: 400,
                    },
                },
            },
            grid: {
                show: true,
                borderColor: "#dddddd",
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true,
                    },
                },
                padding: {
                    top: 5,
                    right: 20,
                },
            },
            fill: {
                opacity: 0.8,
            },
            tooltip: {
                theme: "dark",
            },
        },
    };

    return (
        <Card className={'min-h-[30vh]'}>
            <CardHeader
                floated={false}
                shadow={false}
                className="flex flex-col justify-between gap-4 rounded-none md:flex-row md:items-center relative overflow-visible"
            >
                <div>
                    <Typography variant="h6" color="blue-gray">
                        Most Frequently Caught Profanity
                    </Typography>
                </div>

                <ChartTimeframeSelector chart_name={"profanity-frequency"} setXAxis={setXAxis}
                                        setYAxis={setYAxis}></ChartTimeframeSelector>
            </CardHeader>

            {(yAxis.length !== 0) ?
                (
                    <CardBody className="px-2 pb-0 h-full">
                        <Chart {...chartConfig} />
                    </CardBody>

                )
                :
                (
                    <CardBody className="p-0 flex h-full justify-center">
                        <div className="h-full">
                            <div className={"opacity-25 scale-[0.6]"}>
                                <EnvelopeOpenIcon></EnvelopeOpenIcon>
                            </div>
                            <Typography color="blue-gray" className="font-light">
                                No data for selected duration
                            </Typography>
                        </div>
                    </CardBody>
                )
            }
        </Card>
    );
}
