<template>
  <div>
    <b-container fluid>
      <b-row>
        <b-col cols="4">
          <b-form-group
            id="event_input"
            label="Event"
            label-for="event_input"
            description="Please enter event"
          >
            <b-form-input
              id="input-1"
              v-model="toInsert.name"
              type="email"
              placeholder="Enter email"
              required
            ></b-form-input>
          </b-form-group>

          <div>
            <label for="from-datepicker">From</label>
            <b-form-datepicker
              id="from-datepicker"
              v-model="toInsert.from_date"
              class="mb-2"
            ></b-form-datepicker>
          </div>

          <div>
            <label for="from-datepicker">To</label>
            <b-form-datepicker
              id="to-datepicker"
              v-model="toInsert.to_date"
              class="mb-2"
            ></b-form-datepicker>
          </div>

          <b-form-group
            id="input-group-4"
            v-slot="{ ariaDescribedby }"
            class="text-center section"
          >
            <b-form-checkbox-group
              id="checkboxes-4"
              v-model="selectedDays.checked"
              :aria-describedby="ariaDescribedby"
            >
              <b-form-checkbox value="Monday">Monday</b-form-checkbox>
              <b-form-checkbox value="Tuesday">Tuesday</b-form-checkbox>
              <b-form-checkbox value="Wednesday">Wednesday</b-form-checkbox>
              <b-form-checkbox value="Thursday">Thursday</b-form-checkbox>
              <b-form-checkbox value="Friday">Friday</b-form-checkbox>
              <b-form-checkbox value="Saturday">Saturday</b-form-checkbox>
              <b-form-checkbox value="Sunday">Sunday</b-form-checkbox>
            </b-form-checkbox-group>
          </b-form-group>

          <b-button variant="primary" @click="insertEvent">Submit</b-button>
          <b-button variant="danger" @click="clearFields">Clear</b-button>
        </b-col>
        <b-col cols="8" class="text-center section">
          <h2 class="h2">My Calendar</h2>
          <p class="text-lg font-medium text-gray-600 mb-6">
            Let's save my upcoming events
          </p>
          <v-calendar
            class="custom-calendar max-w-full"
            :masks="masks"
            :attributes="attributes"
            disable-page-swipe
            is-expanded
          >
            <template #day-content="{ day, attributes }">
              <div class="flex flex-col h-full z-10 overflow-hidden">
                <span class="day-label text-sm text-gray-900">{{
                  day.day
                }}</span>
                <div class="flex-grow overflow-y-auto overflow-x-auto">
                  <p
                    v-for="attr in attributes"
                    :key="attr.key"
                    class="text-xs leading-tight rounded-sm p-1 mt-0 mb-1"
                    :class="attr.customData.class"
                  >
                    {{ attr.customData.title }}
                  </p>
                </div>
              </div>
            </template>
          </v-calendar>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'EntryPage',
  data() {
    return {
      masks: {
        weekdays: 'WWW',
      },
      attributes: [],
      toInsert: {
        name: null,
        days: null,
        from_date: null,
        to_date: null,
      },
      selectedDays: {},
    }
  },
  head() {
    return {
      title: `Event Calendar`,
      meta: [
        {
          hid: 'ec',
          name: 'ec',
          content: 'ec',
        },
      ],
    }
  },
  async created() {
    await this.fetchEvents()
  },
  methods: {
    showMessage(msg, variant, title) {
      this.$bvToast.toast(`${msg}`, {
        title,
        toaster: 'b-toaster-bottom-right',
        solid: true,
        variant,
        appendToast: true,
      })
    },
    async insertEvent() {
      try {
        if (!this.selectedDays.checked)
          return this.showMessage(
            'Please select days to apply event!',
            'danger',
            'Error'
          )

        this.toInsert.days = this.selectedDays.checked.toString()

        const res = await axios({
          method: 'POST',
          url: `${this.$axios.defaults.baseURL}/insert-event`,
          data: {
            ...this.toInsert,
          },
        })

        const msg = res.data.msg
        this.showMessage(msg, 'success', 'Success')
        this.clearFields()
        await this.fetchEvents()
      } catch (e) {
        console.log('Error fetch: ', e)
      }
    },
    clearFields() {
      this.selectedDays = {}
      this.toInsert = {
        name: null,
        days: null,
        from_date: null,
        to_date: null,
      }
    },
    async fetchEvents() {
      try {
        const res = await axios({
          method: 'GET',
          url: `${this.$axios.defaults.baseURL}/get-event`,
        })
        this.attributes = []
        const data = res.data.data
        for (let i = 0; i < data.affected.length; i++) {
          const date = data.affected[i]
          this.attributes.push({
            key: i + 1,
            customData: {
              title: data.head.name,
              class: 'bg-green text-white',
            },
            dates: date,
          })
        }
      } catch (e) {
        console.log('Error fetch: ', e)
      }
    },
  },
}
</script>

<style lang="postcss" scoped>
.bg-green {
  background: green;
}
::-webkit-scrollbar {
  width: 0px;
}

::-webkit-scrollbar-track {
  display: none;
}

/deep/ .custom-calendar.vc-container {
  --day-border: 1px solid #b8c2cc;
  --day-border-highlight: 1px solid #b8c2cc;
  --day-width: 90px;
  --day-height: 90px;
  --weekday-bg: #f8fafc;
  --weekday-border: 1px solid #eaeaea;

  border-radius: 0;
  width: 100%;

  & .vc-header {
    background-color: #f1f5f8;
    padding: 10px 0;
  }
  & .vc-weeks {
    padding: 0;
  }
  & .vc-weekday {
    background-color: var(--weekday-bg);
    border-bottom: var(--weekday-border);
    border-top: var(--weekday-border);
    padding: 5px 0;
  }
  & .vc-day {
    padding: 0 5px 3px 5px;
    text-align: left;
    height: var(--day-height);
    min-width: var(--day-width);
    background-color: white;
    &.weekday-1,
    &.weekday-7 {
      background-color: #eff8ff;
    }
    &:not(.on-bottom) {
      border-bottom: var(--day-border);
      &.weekday-1 {
        border-bottom: var(--day-border-highlight);
      }
    }
    &:not(.on-right) {
      border-right: var(--day-border);
    }
  }
  & .vc-day-dots {
    margin-bottom: 5px;
  }
}
</style>
