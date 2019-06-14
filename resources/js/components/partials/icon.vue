<template>
    <div class="icon" :class="[`icon--${name}`, `icon--${size}`, { 'has-align-fix': fixAlign }]">
        <img :src=url>
    </div>
</template>

<script>
    export default {
        props: {
            name: {},
            size: {
                default: 'normal',
            },
            modifier: {
                default: null,
            },
            fixAlign: {
                default: true,
            },
        },
        data: function () {
            return {
                url: '/images/icons/' + this.name + '.svg',
            }
        }
    }
</script>

<style lang="scss">
    .icon {
        position: relative;
        display: inline-block;
        vertical-align: middle;
        width: 0.8rem;
        height: 100%;
        margin: 0 .3rem;
        top: -.05rem;
        fill: currentColor;
        // &.has-align-fix {
        // 	top: -.1rem;
        // }
        &__svg {
            display: inline-block;
            vertical-align: top;
            width: 100%;
            height: 100%;
        }
        &:first-child {
            margin-left: 0;
        }
        &:last-child {
            margin-right: 0;
        }
    }

    // svg sprite
    body > svg,
    .icon use > svg,
    symbol {
        path,
        rect,
        circle,
        g {
            fill: currentColor;
            stroke: none;
        }
        *[d="M0 0h24v24H0z"] {
            display: none;
        }
    }
</style>
