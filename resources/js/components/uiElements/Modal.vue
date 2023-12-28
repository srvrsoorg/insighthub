<template>
    <TransitionRoot
        :show="openModal"
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
    >
        <Dialog
            as="div"
            class="relative z-[99999]"
            @close="$emit('closeModal')"
        >
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div
                    class="fixed inset-0 backdrop-blur-sm bg-gray-500 bg-opacity-30 transition-opacity"
                />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div
                    class="flex min-h-full justify-center p-4 text-center items-center"
                >
                    <TransitionChild
                        class="modal"
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <DialogPanel
                            :class='[
                                "relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-md",
                                ...customClass
                            ]'
                        >
                            <DialogTitle class="flex items-center justify-between p-4 border-b rounded-t">
                                <h2 class="font-medium text-gray-900">
                                    {{ modalTitle }}
                                </h2>
                                <button type="button" @click="$emit('closeModal')" class="text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-700 rounded-lg text-sm w-7 h-7 ml-auto inline-flex justify-center items-center" data-modal-hide="defaultModal">
                                    <i class="fas fa-times text-lg"></i>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </DialogTitle>
                            <div class="px-5 pt-3 pb-5">
                                <slot />
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
export default{
    props:{
        customClass:{
            type:Array,
            default:[]
        },
        openModal:{
            type: Boolean,
            default:false   
        },
        modalTitle:{
            type: String,
            default: ''
        }
    },
    components: {
        Dialog,
        DialogPanel,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
    },
}
</script>
