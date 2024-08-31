import Dropdown from "@/Components/BreezeComponents/Dropdown.jsx";
import {useEffect, useRef, useState} from "react";

export default function ChartTimeframeSelector({chart_name, setXAxis, setYAxis}) {

    const [filters, setFilters] = useState([])
    const [selectedFilter, setSelectedFilter] = useState("")
    const dropdownRef = useRef(null);

    const loadChartData = (chart, value) => {
        //Using axios here instead of router since we are not returning an Inertia response
        axios.get('chart-' + chart, {
            params: {
                'value': value,
            }
        }).then(response => {
            setXAxis(response.data[0])
            setYAxis(response.data[1])
        }).catch(error => {
            console.error(error);
        });
    }


    useEffect(() => {
        //Using axios here instead of router since we are not returning an Inertia response
        axios.get('load-chart-filters').then(response => {
            setFilters(response.data);
            setSelectedFilter(response.data[0])
            loadChartData(chart_name,response.data[0])
        }).catch(error => {
            console.error(error);
        });
    }, []);


    return (
        <Dropdown>
            <Dropdown.Trigger>
                <button
                    ref={dropdownRef}
                    type="button"
                    className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                >
                    {selectedFilter}
                    <svg
                        className="ms-2 -me-0.5 h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fillRule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clipRule="evenodd"
                        />
                    </svg>
                </button>
            </Dropdown.Trigger>

            <Dropdown.Content>
                {filters.map((data) => {
                    return (
                        <Dropdown.Link onClick={(e) => {
                            e.preventDefault();
                            loadChartData(chart_name, data)
                            setSelectedFilter(data)

                            //Automatically clicking the dropdown button to close the dropdown
                            dropdownRef.current.click();
                        }}>
                            {data}
                        </Dropdown.Link>
                    );
                })}
            </Dropdown.Content>
        </Dropdown>
    );
}
