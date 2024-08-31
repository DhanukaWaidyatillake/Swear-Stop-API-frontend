import {Card, CardBody, CardHeader, Typography} from "@material-tailwind/react";
import Chart from "react-apexcharts";
import ChartTimeframeSelector from "@/Components/Dashboard/ChartTimeframeSelector.jsx";


const chartConfig = {
    type: "pie",
    width: 280,
    height: 280,
    series: [44, 55, 13, 43, 22],
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
        colors: ["#020617", "#ff8f00", "#00897b", "#1e88e5", "#d81b60"],
        legend: {
            show: false,
        },
    },
};

export default function NoOfBannedWordsCategory() {
    return (
        <Card>
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

                <ChartTimeframeSelector></ChartTimeframeSelector>

            </CardHeader>
            <CardBody className="mt-4 grid place-items-center px-2">
                <Chart {...chartConfig} />
            </CardBody>
        </Card>
    );
}
