<template>
  <section class="timeline">
    <div v-if="hasItems" class="wrapper-timeline">
      <div
        v-for="(timelineContent, timelineIndex) in dataTimeline"
        :key="timelineIndex"
        :class="wrapperItemClass(timelineIndex)"
      >
        <div class="section-year">
          <p v-if="hasYear(timelineContent)" class="year">
            {{ getYear(timelineContent) }}
          </p>
        </div>
        <TimelineItem
          :item-timeline="timelineContent"
          :date-locale="dateLocale"
          :color-dots="colorDots"
        />
      </div>
    </div>
    <p v-else>{{ messageWhenNoItems }}</p>
  </section>
</template>

<script>
import TimelineItem from './TimelineItem.vue'

export default {
  name: 'Timeline',
  components: {
    TimelineItem
  },
  props: {
    timelineItems: {
      type: Array,
      default: () => []
    },
    messageWhenNoItems: {
      type: String,
      default: ''
    },
    colorDots: {
      type: String,
      default: '#2da1bf'
    },
    uniqueTimeline: {
      type: Boolean,
      default: false
    },
    uniqueYear: {
      type: Boolean,
      default: false
    },
    order: {
      type: String,
      default: ''
    },
    dateLocale: {
      type: String,
      default: ''
    }
  },
  computed: {
    hasItems() {
      return !!this.timelineItems.length
    },
    dataTimeline() {
      if (this.order === 'desc')
        return this.orderItems(this.timelineItems, 'desc')
      if (this.order === 'asc')
        return this.orderItems(this.timelineItems, 'asc')
      return this.timelineItems
    }
  },
  methods: {
    wrapperItemClass(timelineIndex) {
      const isSameYearPreviousAndCurrent = this.checkYearTimelineItem(
        timelineIndex
      )
      const isUniqueYear =
        this.uniqueYear &&
        isSameYearPreviousAndCurrent &&
        this.order !== undefined
      return {
        'wrapper-item': true,
        'unique-timeline': this.uniqueTimeline || isUniqueYear
      }
    },
    checkYearTimelineItem(timelineIndex) {
      const previousItem = this.dataTimeline[timelineIndex - 1]
      const nextItem = this.dataTimeline[timelineIndex + 1]
      const currentItem = this.dataTimeline[timelineIndex]
      if (!previousItem || !nextItem) {
        return false
      }
      const fullPreviousYear = this.getYear(previousItem)
      const fullNextYear = this.getYear(nextItem)
      const fullCurrentYear = this.getYear(currentItem)
      return (
        (fullPreviousYear === fullCurrentYear &&
          fullCurrentYear === fullNextYear) ||
        fullCurrentYear === fullNextYear
      )
    },
    getYear(date) {
      return date.from.getFullYear()
    },
    hasYear(dataTimeline) {
      return (
        dataTimeline.hasOwnProperty('from') && dataTimeline.from !== undefined
      )
    },
    getTimelineItemsAssembled(items) {
      const itemsGroupByYear = []
      items.forEach(item => {
        const fullTime = item.from.getTime()
        if (itemsGroupByYear[fullTime]) {
          return itemsGroupByYear[fullTime].push(item)
        }
        itemsGroupByYear[fullTime] = [item]
      })
      return itemsGroupByYear
    },
    orderItems(items, typeOrder) {
      const itemsGrouped = this.getTimelineItemsAssembled(items)
      const keysItemsGrouped = Object.keys(itemsGrouped)
      const timeItemsOrdered = keysItemsGrouped.sort((a, b) => {
        if (typeOrder === 'desc') {
          return b - a
        }
        return a - b
      })
      return timeItemsOrdered.map(timeItem => itemsGrouped[timeItem]).flat()
    }
  }
}
</script>

<style lang="scss" scoped>
.timeline {
  text-align: left;
  width: 100%;
  max-width: 500px;
  .wrapper-timeline {
    position: relative;
  }
  .wrapper-item {
    display: grid;
    grid-template-columns: 100px 1fr;
    margin-bottom: 20px;
    .section-year {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      align-items: flex-end;
      padding: 15px;
      font-weight: bold;
      font-size: 18px;
      .year {
        margin: 0;
      }
    }
    &.unique-timeline {
      margin-bottom: 0;
    }
  }
}
</style>
