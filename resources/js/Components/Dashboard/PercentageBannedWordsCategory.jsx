import {Card, CardBody, CardHeader, Typography} from "@material-tailwind/react";
import Chart from "react-apexcharts";
import ChartTimeframeSelector from "@/Components/Dashboard/ChartTimeframeSelector.jsx";
import {useState} from "react";
import {EnvelopeOpenIcon} from "@heroicons/react/24/outline/index.js";


export default function PercentageBannedWordsCategory() {

    const [xAxis, setXAxis] = useState([]);
    const [yAxis, setYAxis] = useState([]);

    const chartConfig = {
        type: "pie",
        width: 280,
        height: 280,
        series: yAxis,
        options: {
            chart: {
                toolbar: {
                    show: false,
                },
            },
            labels: xAxis,
            title: {
                show: "",
            },
            dataLabels: {
                enabled: false,
            },
            colors: ["#020617", "#ff8f00", "#00897b", "#1e88e5", "#d81b60"],
            legend: {
                show: false,
            },
        },
    };

    return (
        <Card className={'min-h-[45vh]'}>
            <CardHeader
                floated={false}
                shadow={false}
                color="transparent"
                className="flex flex-col gap-4 rounded-none md:flex-row md:items-center relative overflow-visible"
            >
                <div>
                    <Typography variant="h6" color="blue-gray">
                        Percentage of Profanity Caught In Each Category
                    </Typography>
                </div>

                <ChartTimeframeSelector chart_name={"profanity-category"} setXAxis={setXAxis}
                                        setYAxis={setYAxis}></ChartTimeframeSelector>

            </CardHeader>

            {(yAxis.length !== 0) ?
                (
                    <CardBody className="mt-4 grid place-items-center px-2">
                        <Chart {...chartConfig} />
                    </CardBody>
                )
                :
                (
                    <CardBody className="p-0 flex h-full justify-center">
                        <div className="h-full mt-5">
                            <div className={"opacity-25 scale-[0.7]"}>
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
