import {
    Button,
    Card,
    CardBody,
    CardFooter,
    CardHeader,
    Chip,
    IconButton,
    Tab,
    Tabs,
    TabsHeader,
    Typography,
} from "@material-tailwind/react";

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

const TABLE_HEAD = ["Text", "Profanity detected", "Metadata", "Timestamp", ""];

const TABLE_ROWS = [
    {
        text: "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec qu",
        words: ["Fuck","Shit","Hell"],
        job: "Manager",
        org: "Organization",
        online: true,
        date: "23/04/18",
    },
    {
        text: "Sed ut s error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta.\n" +
            "\n",
        words: ["Ass","Cunt"],
        job: "Programator",
        org: "Developer",
        online: false,
        date: "23/04/18",
    },
    {
        text: "But I must f the system, and expound the actual teachings of the gre\n" +
            "\n",
        words: ["Hoe","Sh!@t","Pervert"],
        job: "Executive",
        org: "Projects",
        online: false,
        date: "19/09/17",
    },
    {
        text: "Li Europan lingues es membres del sam familie. Lor separat existentie es un myth. Por scientie, musica, sport etc, litot Europa usa li sam vocabular. Li lingues differe solmen in li grammatica, li pro\n" +
            "\n",
        words: ["Ass","Cunt"],
        job: "Programator",
        org: "Developer",
        online: true,
        date: "24/12/08",
    },
    {
        text: "kalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large.\n" +
            "\n",
        words: ["Fuck","Shit","Hell"],
        job: "Manager",
        org: "Executive",
        online: false,
        date: "04/10/21",
    },
];

export default function RequestsTable(props) {
    return (
        <Card className="h-full w-full">
            <CardHeader floated={false} shadow={false} className="rounded-none">
                <div className="mb-8 flex items-center justify-between gap-8">
                    <div>
                        <Typography variant="h5" color="blue-gray">
                            Recent Requests
                        </Typography>
                    </div>

                </div>
                <Tabs value="all" className="w-full md:w-max">
                    <TabsHeader>
                        {TABS.map(({label, value}) => (
                            <Tab key={value} value={value} className="w-max">
                                &nbsp;&nbsp;{label}&nbsp;&nbsp;
                            </Tab>
                        ))}
                    </TabsHeader>
                </Tabs>
            </CardHeader>
            <CardBody className="h-full w-full">
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
                    {TABLE_ROWS.map(({ text, words, date }, index) => {
                        const isLast = index === TABLE_ROWS.length - 1;
                        const classes = isLast ? "p-4" : "p-4 border-b border-blue-gray-50";

                        return (
                            <tr key={text}>
                                <td className={`${classes}`}>
                                    <Typography variant="small" color="blue-gray" className="font-normal max-w-64">
                                        {text}
                                    </Typography>
                                </td>
                                <td className={`${classes} bg-blue-gray-50/50`}>
                                    <Typography variant="small" color="blue-gray" className="font-normal flex">
                                        {words.map((item,index) => {
                                           return  <Chip
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
                                    <Typography variant="small" color="blue-gray" className="font-bold">
                                        {words[0]}
                                    </Typography>
                                </td>
                                <td className={classes}>
                                    <Typography variant="small" color="blue-gray" className="font-normal">
                                        {date}
                                    </Typography>
                                </td>
                                <td className={`${classes} bg-blue-gray-50/50 text-center`}>
                                    <Typography as="a" href="#" variant="small" color="blue-gray"
                                                className="font-medium">
                                        Show Details
                                    </Typography>
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
            </CardBody>
            <CardFooter className="flex items-center justify-between border-t border-blue-gray-50 p-4">
                <Button variant="outlined" size="sm">
                    Previous
                </Button>
                <div className="flex items-center gap-2">
                    <IconButton variant="outlined" size="sm">
                        1
                    </IconButton>
                    <IconButton variant="text" size="sm">
                        2
                    </IconButton>
                    <IconButton variant="text" size="sm">
                        3
                    </IconButton>
                    <IconButton variant="text" size="sm">
                        ...
                    </IconButton>
                    <IconButton variant="text" size="sm">
                        233
                    </IconButton>
                    <IconButton variant="text" size="sm">
                        234
                    </IconButton>
                    <IconButton variant="text" size="sm">
                        235
                    </IconButton>
                </div>
                <Button variant="outlined" size="sm">
                    Next
                </Button>
            </CardFooter>
        </Card>
    );
}
