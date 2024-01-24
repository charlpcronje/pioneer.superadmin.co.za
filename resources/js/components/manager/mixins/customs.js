import EventBus from "../../../eventBus";

export default {

    computed: {
        selected() {
            return this.$store.state.fm[this.manager].selected;
        },
    },
    methods: {
        addMetadata(item, event){
           // const type = item.type === 'dir' ? 'directories' : 'files';
            location.href='metadata?type='+item.type+'&path='+item.path;

        },
        roleSelection(item, event) {
            // el type
             const type = item.type === 'dir' ? 'directories' : 'files';
            // search in selected array
             const alreadySelected = this.selected[type].includes(item.path);

            // select this element
            if (!alreadySelected) {
                // select item
                this.$store.commit(`fm/${this.manager}/changeSelected`, {
                    type,
                    path: item.path,
                });
            }

            // create event
            EventBus.$emit('roleSelection', event);
        },
    }
}
