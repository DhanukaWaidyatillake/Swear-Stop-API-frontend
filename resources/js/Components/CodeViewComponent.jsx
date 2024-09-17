import { useState } from "react";

export default function CodeViewComponent({
    json = [],
    max_height = 36,
    title = "",
}) {

    const renderJson = (data, depth = 1) => {
        return Object.keys(data).map((key, index) => {
            const value = data[key];
            if (Array.isArray(value)) {
                return (
                    <div key={index}>
                        {getSpans(depth)}"{key}": [<br />
                        {value.map((item, i) => (
                            <div key={i}>
                                {getSpans(depth + 1)}
                                {JSON.stringify(item)},<br />
                            </div>
                        ))}
                        {getSpans(depth)}],
                        <br />
                    </div>
                );
            } else if (typeof value === "object" && value !== null) {
                return (
                    <div key={index}>
                        {getSpans(depth)}"{key}": {"{"}
                        <br />
                        {renderJson(value, depth + 1)}
                        {getSpans(depth)}
                        {"}"},<br />
                    </div>
                );
            } else {
                return (
                    <div key={index}>
                        {getSpans(depth)}"{key}": "{value}",
                        <br />
                    </div>
                );
            }
        });
    };

    const getSpans = (num) => {
        let spans = [];
        for (let i = 0; i < num; i++) {
            spans.push(<span key={i}>&nbsp;&nbsp;&nbsp;</span>);
        }
        return spans;
    };

    return (
        <div className="bg-black text-white p-4 rounded-xl">
            <div className="flex justify-between items-center mb-2">
                <span className="text-gray-400">{title}</span>
            </div>
            <div className="overflow-x-auto scrollbar-thin">
                <pre id="code" className={`text-white max-w-0 h-${max_height}`}>
                    <code>
                        {"{"} <br />
                        {json ? renderJson(json) : ""}
                        {"}"}
                    </code>
                </pre>
            </div>
        </div>
    );
}
