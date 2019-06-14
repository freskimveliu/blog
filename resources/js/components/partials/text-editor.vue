<template>
    <div class="editor">
        <editor-menu-bar :editor="editor" v-slot="{ commands, isActive }">
            <div class="menubar">

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.bold() }"
                        @click="commands.bold"
                >
                    <icon name="bold"/>
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.italic() }"
                        @click="commands.italic"
                >
                    <icon name="italic"/>
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.strike() }"
                        @click="commands.strike"
                >
                    <icon name="strike"/>
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.underline() }"
                        @click="commands.underline"
                >
                    <icon name="underline"/>
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.code() }"
                        @click="commands.code"
                >
                    <icon name="code"/>
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.paragraph() }"
                        @click="commands.paragraph"
                >
                    <icon name="paragraph"/>
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.heading({ level: 1 }) }"
                        @click="commands.heading({ level: 1 })"
                >
                    H1
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.heading({ level: 2 }) }"
                        @click="commands.heading({ level: 2 })"
                >
                    H2
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.heading({ level: 3 }) }"
                        @click="commands.heading({ level: 3 })"
                >
                    H3
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.bullet_list() }"
                        @click="commands.bullet_list"
                >
                    <icon name="ul"/>
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.ordered_list() }"
                        @click="commands.ordered_list"
                >
                    <icon name="ol"/>
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.blockquote() }"
                        @click="commands.blockquote"
                >
                    <icon name="quote"/>
                </button>

                <button
                        class="menubar__button"
                        :class="{ 'is-active': isActive.code_block() }"
                        @click="commands.code_block"
                >
                    <icon name="code"/>
                </button>

                <button
                        class="menubar__button"
                        @click="commands.horizontal_rule"
                >
                    <icon name="hr"/>
                </button>

                <button
                        class="menubar__button"
                        @click="commands.undo"
                >
                    <icon name="undo"/>
                </button>

                <button
                        class="menubar__button"
                        @click="commands.redo"
                >
                    <icon name="redo"/>
                </button>

                <button
                        class="menubar__button"
                        @click="showImagePrompt(commands.image)"
                >
                    <icon name="image"/>
                </button>
                <button
                        class="menubar__button"
                        @click="commands.createTable({rowsCount: 3, colsCount: 3, withHeaderRow: false })"
                >
                    <icon name="table" />
                </button>

                <span v-if="isActive.table()">
						<button
                                class="menubar__button"
                                @click="commands.deleteTable"
                        >
							<icon name="delete_table" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.addColumnBefore"
                        >
							<icon name="add_col_before" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.addColumnAfter"
                        >
							<icon name="add_col_after" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.deleteColumn"
                        >
							<icon name="delete_col" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.addRowBefore"
                        >
							<icon name="add_row_before" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.addRowAfter"
                        >
							<icon name="add_row_after" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.deleteRow"
                        >
							<icon name="delete_row" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.toggleCellMerge"
                        >
							<icon name="combine_cells" />
						</button>
                </span>
            </div>
        </editor-menu-bar>

        <editor-content class="editor__content" :editor="editor"/>
    </div>
</template>


<script>
    // Import the editor
    import {Editor, EditorContent, EditorMenuBar} from 'tiptap'
    import Icon from './icon'
    import {
        Blockquote,
        CodeBlock,
        HardBreak,
        Heading,
        HorizontalRule,
        OrderedList,
        BulletList,
        ListItem,
        TodoItem,
        TodoList,
        Bold,
        Code,
        Italic,
        Link,
        Strike,
        Underline,
        History,
        Image,
        Table,
        TableHeader,
        TableCell,
        TableRow,
    } from 'tiptap-extensions'

    export default {
        components: {
            EditorContent,
            EditorMenuBar,
            Icon,
        },
        props: ['value'],
        data() {
            return {
                editor: null,
            }
        },
        mounted() {
            this.editor = new Editor({
                extensions: [
                    new Blockquote(),
                    new BulletList(),
                    new CodeBlock(),
                    new HardBreak(),
                    new Heading({levels: [1, 2, 3]}),
                    new HorizontalRule(),
                    new ListItem(),
                    new OrderedList(),
                    new TodoItem(),
                    new TodoList(),
                    new Link(),
                    new Bold(),
                    new Code(),
                    new Italic(),
                    new Strike(),
                    new Underline(),
                    new History(),
                    new Image(),
                    new Table({
                        resizable: true,
                    }),
                    new TableHeader(),
                    new TableCell(),
                    new TableRow(),
                ],
                onUpdate: ({getHTML}) => {
                    this.$emit('input', getHTML())
                },
            });
            this.editor.setContent(this.value)
        },
        beforeDestroy() {
            this.editor.destroy()
        },
        watch: {
            value(val) {
                // so cursor doesn't jump to start on typing
                if (val !== this.editor.getHTML()) {
                    this.editor.setContent(val)
                }
            }
        },
        methods: {
            showImagePrompt(command) {
                const src = prompt('Enter the url of your image here')
                if (src !== null) {
                    command({src})
                }
            },
        }
    }
</script>
<style>
    .ProseMirror {
        display: block;
        width: 100%;
        height: auto;
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
        font-weight: 400;
        line-height: 1.6;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        min-height: 150px;
    }

    img {
        max-width: 100%;
    }

</style>

