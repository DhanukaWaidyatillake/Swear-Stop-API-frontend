import InputLabel from "@/Components/BreezeComponents/InputLabel.jsx";
import TextInput from "@/Components/BreezeComponents/TextInput.jsx";
import {IconButton, Popover, PopoverContent, PopoverHandler} from "@material-tailwind/react";
import ClipboardIcon from "@/Icons/ClipboardIcon.jsx";
import RegenerateIconSVG from "@/Icons/RegenerateIconSVG.jsx";
import {Link} from "@inertiajs/react";

export default function APIKeyForm({api_key}) {


    return (
        <section className="max-w-xl">
            <header>
                <h2 className="text-lg font-medium text-gray-900">API Key</h2>

                <p className="mt-1 text-sm text-gray-600">
                    Copy / Refresh your API key
                </p>
            </header>

            <div className="mt-6 space-y-6">
                <div>
                    <InputLabel htmlFor="api_key" value="API Key"/>

                    <div className="flex">
                        <TextInput
                            id="api_key"
                            className="mt-1 block w-full disabled disabled:opacity-75"
                            value={api_key}
                            required
                            disabled
                            focused
                        />

                        <Popover
                            animate={{
                                mount: {scale: 1, y: -2},
                                unmount: {scale: 0, y: 25},
                            }}
                        >
                            <PopoverHandler onClick={() => {
                                navigator.clipboard.writeText(api_key)
                            }}>
                                <IconButton variant="outlined" size="sm" className="ml-5 mt-2">
                                    <div className="scale-75">
                                        <ClipboardIcon></ClipboardIcon>
                                    </div>
                                </IconButton>
                            </PopoverHandler>
                            <PopoverContent>
                                Copied!
                            </PopoverContent>
                        </Popover>

                        <Link href={route('profile.refresh-token')} as="button" method="post" preserveScroll>
                            <IconButton variant="outlined" size="sm" className="ml-5">
                                <div className="scale-75">
                                    <RegenerateIconSVG></RegenerateIconSVG>
                                </div>
                            </IconButton>
                        </Link>

                    </div>
                </div>
            </div>
        </section>
    );
}
