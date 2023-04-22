import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.start();

if (typeof Alpine !== 'undefined') {
    /**
     * Wenn Alpinejs bereits verfügbar ist...
     */
    addAlpineDirective();
} else {
    document.addEventListener('alpine:init', () => {
        /**
         * Wenn Alpinejs verfügbar ist...
         */
        addAlpineDirective();
    })
}

/**
 * Alpinejs directive um pjax/jquery murks zu verhindern...
 */
function addAlpineDirective() {
    Alpine.directive('repeater', (el) => {
        setTimeout(() => {
            el.dispatchEvent(new CustomEvent('repeater:ready'))
        })
    })
}

window.repeater = () => {
    return {
        groups: [],
        value: '',
        initialValue: [],
        index: 0,
        groupIndex: 0,
        $alpineLoader: document.querySelector('.alpine-loader'),
        addGroup(position) {
            /**
             * 0 = top
             * 1 = bottom
             */

            /**
             * Objekt entsprechend der Gruppen-Felddefinitionen
             */
            let obj = {};

            if (repeaterGroup) {
                /**
                 * clone obj
                 */
                obj = JSON.parse(JSON.stringify(repeaterGroup));
            }

            if (position) {
                this.groups.push(obj);
            } else {
                this.groups.unshift(obj);
            }
        },
        addFields(index) {
            /**
             * Objekt entsprechend der Felddefinitionen
             */
            // eslint-disable-next-line no-undef
            if (repeaterFields) {
                /**
                 * clone obj
                 */
                // eslint-disable-next-line no-undef
                this.groups[index].fields.push(JSON.parse(JSON.stringify(repeaterFields)));
            }
        },
        removeGroup(index) {
            this.groups.splice(index, 1);
            this.updateValues();
        },
        removeField(groupIndex, fieldIndex) {
            this.groups[groupIndex].fields.splice(fieldIndex, 1);
            this.updateValues();
        },
        updateValues() {
            /**
             * Gruppen werden als String im value-Model gespeichert...
             */
            this.value = JSON.stringify(this.groups);
        },
        setInitialValue() {
            /**
             * Vorhanden Daten setzen...
             */
            // eslint-disable-next-line no-undef
            this.initialValue = initialValues;
            this.groups = [];

            if (this.initialValue) {
                this.groups = this.initialValue;
                this.value = JSON.stringify(this.groups);
            }

            this.$nextTick(() => {
                this.$alpineLoader.classList.remove('rex-visible');
            });
        },
        groupHasFields(groupIndex) {
            return this.groups[groupIndex].hasOwnProperty('fields') && this.groups[groupIndex].fields.length > 0;
        },
        moveGroup(from, to) {
            this.groups.splice(to, 0, this.groups.splice(from, 1)[0]);
            this.updateValues();
        },
        moveField(groupIndex, from, to) {
            this.groups[groupIndex].fields.splice(to, 0, this.groups[groupIndex].fields.splice(from, 1)[0]);
            this.updateValues();
        },
        addLink(id, groupIndex, index, fieldName) {
            // eslint-disable-next-line no-undef
            let linkMap = openLinkMap(id);
            /**
             * man kann nur via jQuery auf jQuery events hören...
             */
            $(linkMap).on('rex:selectLink', (event, linkurl, linktext) => {
                if (this.groupHasFields(groupIndex)) {
                    if (typeof this.groups[groupIndex].fields[index][fieldName] !== 'object') {
                        this.groups[groupIndex].fields[index][fieldName] = {};
                    }

                    this.groups[groupIndex].fields[index][fieldName]['id'] = linkurl.replace('redaxo://', '');
                    this.groups[groupIndex].fields[index][fieldName]['name'] = linktext;
                } else {
                    if (typeof this.groups[groupIndex][fieldName] !== 'object') {
                        this.groups[groupIndex][fieldName] = {};
                    }

                    this.groups[groupIndex][fieldName]['id'] = linkurl.replace('redaxo://', '');
                    this.groups[groupIndex][fieldName]['name'] = linktext;
                }
                this.updateValues();
            });

            return false;
        },
        removeLink(groupIndex, index, fieldName) {
            if (this.groupHasFields(groupIndex)) {
                this.groups[groupIndex].fields[index][fieldName]['id'] = '';
                this.groups[groupIndex].fields[index][fieldName]['name'] = '';
            } else {
                this.groups[groupIndex][fieldName]['id'] = '';
                this.groups[groupIndex][fieldName]['name'] = '';
            }
            this.updateValues();
        },
        addImage(id, groupIndex, index, fieldName) {
            // eslint-disable-next-line no-undef
            const media = addREXMedia(id);
            $(media).on('rex:selectMedia', (event, mediaName) => {
                this.groups[groupIndex].fields[index][fieldName] = mediaName;
                this.updateValues();
            });
            return false;
        },
        selectImage(id, groupIndex, index, fieldName) {
            // eslint-disable-next-line no-undef
            const media = openREXMedia(id);
            $(media).on('rex:selectMedia', (event, mediaName) => {
                this.groups[groupIndex].fields[index][fieldName] = mediaName;
                this.updateValues();
            });
            return false;
        },
        deleteImage(id, groupIndex, index, fieldName) {
            // eslint-disable-next-line no-undef
            deleteREXMedia(id);
            this.groups[groupIndex].fields[index][fieldName] = '';
            this.updateValues();
        },
    }
}
